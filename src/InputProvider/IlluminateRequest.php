<?php

namespace SportSpar\Grids\InputProvider;

use Illuminate\Http\Request;
use Request as RequestFacade;

class IlluminateRequest implements InputProviderInterface
{
    /**
     * @var array
     */
    private $input;

    /**
     * @var string
     */
    private $key;

    /**
     * @var Request;
     */
    private $request;

    /**
     * @param string       $key
     * @param Request|null $request
     */
    public function __construct(string $key, Request $request = null)
    {
        $this->key = $key;

        // Use provided request or load via Facade
        if (null !== $request) {
            $this->request = $request;
        } else {
            $this->request = RequestFacade::instance();
        }

        $this->input = $this->request->get($this->getKey(), []);
    }

    /**
     * Returns input related to grid.
     *
     * @return array
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Returns input key for grid parameters.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Returns sorting parameters passed to input.
     *
     * @return mixed
     */
    public function getSorting()
    {
        return $this->getValue('sort');
    }

    /**
     * @return string
     */
    public function getSortingHiddenInputsHtml(): string
    {
        $html = '';

        $key = $this->getKey();
        if (isset($this->input['sort'])) {
            foreach ($this->input['sort'] as $field => $direction) {
                $html .= sprintf('<input name="%s[sort][%s]" type="hidden" value="%s">', $key, $field, $direction);
            }
        }

        return $html;
    }

    /**
     * Returns UID for current grid state.
     *
     * Currently used as key for caching.
     *
     * @return string
     */
    public function getUniqueRequestId()
    {
        $cookiesStr = '';

        foreach ($_COOKIE as $key => $val) {
            if (strpos($key, $this->getKey()) !== false) {
                $cookiesStr .= $key . json_encode($val);
            }
        }

        return md5($cookiesStr . $this->getKey() . json_encode($this->getInput()));
    }

    /**
     * @param string $column
     * @param string $direction
     *
     * @return self
     */
    public function setSorting($column, $direction)
    {
        $this->input['sort'] = [$column => $direction];

        return $this;
    }

    /**
     * @param string $filterName
     * @param string $value
     *
     * @return self
     */
    public function setFilterValue($filterName, $value)
    {
        $this->input['filters'][$filterName] = $value;

        return $this;
    }

    /**
     * Returns input value for filter.
     *
     * @param string $filterName
     *
     * @return string|null
     */
    public function getFilterValue($filterName)
    {
        return $this->input['filters'][$filterName] ?? null;
    }

    /**
     * Returns value of input parameter related to grid.
     *
     * @param string $key
     * @param $default
     *
     * @return mixed
     */
    public function getValue($key, $default = null)
    {
        return $this->input[$key] ?? $default;
    }

    /**
     * Returns current query string extended by specified GET parameters.
     *
     * @param array $newParams
     *
     * @return string
     */
    private function getQueryString(array $newParams = []): string
    {
        $params = $this->request->input();
        if (!empty($this->input)) {
            $params[$this->getKey()] = $this->input;
        }
        if (!empty($newParams)) {
            if (empty($params[$this->getKey()])) {
                $params[$this->getKey()] = [];
            }
            foreach ($newParams as $key => $value) {
                $params[$this->getKey()][$key] = $value;
            }
        }

        return http_build_query($params);
    }

    /**
     * Returns current URL extended by specified GET parameters.
     *
     * @param array $newParams
     *
     * @return string
     */
    public function getUrl(array $newParams = []): string
    {
        if (null !== $queryString = $this->getQueryString($newParams)) {
            $queryString = '?' . $queryString;
        }

        return $this->request->getSchemeAndHttpHost()
            . $this->request->getBaseUrl()
            . $this->request->getPathInfo()
            . $queryString;
    }
}
