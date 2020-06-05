<?php

namespace SportSpar\Grids\Components;

use Event;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;
use SportSpar\Grids\Components\Base\RenderableComponent;
use SportSpar\Grids\Components\Base\RenderableRegistry;
use SportSpar\Grids\DataProvider\AbstractDataProvider;
use SportSpar\Grids\Grid;

/**
 * Class CsvExport
 *
 * The component provides control for exporting data to CSV.
 *
 * @author: Vitaliy Ofat <i@vitaliy-ofat.com>
 * @package SportSpar\Grids\Components
 */
class CsvExport extends RenderableComponent
{
    const NAME = 'csv_export';
    const INPUT_PARAM = 'csv';
    const CSV_DELIMITER = ';';
    const CSV_EXT = '.csv';
    const DEFAULT_ROWS_LIMIT = 5000;

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
     * @var int
     */
    protected $rowsLimit = self::DEFAULT_ROWS_LIMIT;

    /**
     * @var string
     */
    protected $output;

    /**
     * @var string
     */
    protected $fileName;

    /**
     * @param Grid $grid
     * @return null|void
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
     * @return int
     */
    public function getRowsLimit(): int
    {
        return $this->rowsLimit;
    }

    /**
     * @param int $limit
     *
     * @return self
     */
    public function setRowsLimit($limit): self
    {
        $this->rowsLimit = $limit;
        return $this;
    }

    protected function setCsvHeaders(Response $response)
    {
        $response->header('Content-Type', 'application/csv');
        $response->header('Content-Disposition', 'attachment; filename=' . $this->getFileName());
        $response->header('Pragma', 'no-cache');
    }

    protected function resetPagination(AbstractDataProvider $provider)
    {
        if (version_compare(Application::VERSION, '5.0.0', '<')) {
            $provider->getPaginationFactory()->setPageName('page_unused');
        } else {
            Paginator::currentPageResolver(function () {
                return 1;
            });
        }
        $provider->setPageSize($this->getRowsLimit());
        $provider->setCurrentPage(1);
    }

    protected function renderCsv()
    {
        // Clean output buffer from the rendered view (whatever there is before the grid)
        ob_clean();
        $file = fopen('php://output', 'wb');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="'. $this->getFileName() .'"');
        header('Pragma: no-cache');

        set_time_limit(0);

        $provider = $this->grid->getConfig()->getDataProvider();

        $header = $this->renderHeader();
        fputcsv($file, $header, static::CSV_DELIMITER);

        $this->resetPagination($provider);
        $provider->reset();

        while ($row = $provider->getRow()) {
            $output = [];
            foreach ($this->grid->getConfig()->getColumns() as $column) {
                if (!$column->isHidden()) {
                    $output[] = $this->escapeString($column->getValue($row));
                }
            }
            fputcsv($file, $output, static::CSV_DELIMITER);
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
        $str = preg_replace('/\s+/', ' ', $str); # remove double spaces
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
            if (!$column->isHidden()) {
                $output[] = $this->escapeString($column->getLabel());
            }
        }

        return $output;
    }
}
