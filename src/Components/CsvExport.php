<?php

namespace SportSpar\Grids\Components;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use SportSpar\Grids\Components\Base\RenderableComponent;
use SportSpar\Grids\Components\Base\RenderableRegistry;
use SportSpar\Grids\FieldConfig;
use SportSpar\Grids\Grid;

/**
 * Class CsvExport
 *
 * The component provides control for exporting data to CSV.
 *
 * @author: Vitaliy Ofat <i@vitaliy-ofat.com>
 */
class CsvExport extends RenderableComponent
{
    const NAME = 'csv_export';
    const INPUT_PARAM = 'csv';
    const CSV_EXT = '.csv';
    const UTF8_BOM = "\xEF\xBB\xBF";

    /**
     * @var string
     */
    protected $template = '*.components.csv_export';

    /**
     * @var string
     */
    protected $name = CsvExport::NAME;

    /**
     * @var string
     */
    protected $renderSection = RenderableRegistry::SECTION_END;

    /**
     * @var string
     */
    private $csvDelimiter = ';';

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var string[]
     */
    private $excludeColumns = [];

    /**
     * @var bool
     */
    private $includeBOM = true;

    /**
     * @param Grid $grid
     *
     * @return void|null
     */
    public function initialize(Grid $grid)
    {
        parent::initialize($grid);
        Event::listen(Grid::EVENT_PREPARE, function (Grid $grid) {
            if ($this->grid !== $grid) {
                return;
            }
            if ($grid->getInputProcessor()->getValue(static::INPUT_PARAM, false)) {
                $this->renderCsv();
            }
        });
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setFileName($name): self
    {
        $this->fileName = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName . static::CSV_EXT;
    }

    /**
     * @return string[]
     */
    public function getExcludeColumns(): array
    {
        return $this->excludeColumns;
    }

    /**
     * @param string[] $excludeColumns
     */
    public function setExcludeColumns(array $excludeColumns)
    {
        $this->excludeColumns = $excludeColumns;
    }

    /**
     * @return bool
     */
    public function isIncludeBOM(): bool
    {
        return $this->includeBOM;
    }

    /**
     * @param bool $includeBOM
     */
    public function setIncludeBOM(bool $includeBOM)
    {
        $this->includeBOM = $includeBOM;
    }

    protected function setCsvHeaders(Response $response)
    {
        $response->header('Content-Type', 'application/csv');
        $response->header('Content-Disposition', 'attachment; filename=' . $this->getFileName());
        $response->header('Pragma', 'no-cache');
    }

    protected function renderCsv()
    {
        // Laravel stacks every section with output buffers,
        // clean every buffer possible before outputting the csv
        while (ob_get_level()) {
            ob_end_clean();
        }
        $file = fopen('php://output', 'wb');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $this->getFileName() . '"');
        header('Pragma: no-cache');

        set_time_limit(0);

        $provider = $this->grid->getConfig()->getDataProvider();

        if ($this->isIncludeBOM()) {
            fwrite($file, self::UTF8_BOM);
        }

        $header = $this->renderHeader();
        fputcsv($file, $header, $this->csvDelimiter);

        foreach ($provider->getAllRows() as $row) {
            $output = [];
            foreach ($this->grid->getConfig()->getColumns() as $column) {
                if ($this->shouldRenderColumn($column)) {
                    $output[] = $this->escapeString($column->getValue($row));
                }
            }
            fputcsv($file, $output, $this->csvDelimiter);
        }

        fclose($file);
        exit;
    }

    /**
     * @param string $str
     *
     * @return string
     */
    protected function escapeString($str): string
    {
        $str = html_entity_decode($str);
        $str = strip_tags($str);
        $str = str_replace('"', '\'', $str);
        $str = preg_replace('/\s+/', ' ', $str); // remove double spaces
        $str = trim($str);

        return $str;
    }

    /**
     * @return array
     */
    protected function renderHeader(): array
    {
        $output = [];

        foreach ($this->grid->getConfig()->getColumns() as $column) {
            if ($this->shouldRenderColumn($column)) {
                $output[] = $this->escapeString($column->getLabel());
            }
        }

        return $output;
    }

    /**
     * @param FieldConfig $column
     *
     * @return bool
     */
    private function shouldRenderColumn(FieldConfig $column): bool
    {
        if (in_array($column->getName(), $this->excludeColumns, true)) {
            return false;
        }

        // Skip hidden columns
        if ($column->isHidden()) {
            return false;
        }

        return true;
    }
}
