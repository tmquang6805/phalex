<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Mvc;

use Phalcon\Mvc\View as PhalconView;
use Phalcon\Mvc\View\Engine;
use Phalex\Di\Di;

/**
 * Description of View
 *
 * @author quangtm
 */
class View extends PhalconView
{
    /**
     * Constructor for phalex view
     *
     * <ul>
     *      <li>array['di']         <i>\Phalex\Di\Di</i>    REQUIRED</li>
     *      <li>array['views_dir']  <i>string</i>           Sets views directory. Depending of your platform,
     *                                                      always add a trailing slash or backslash. REQUIRED</li>
     *      <li>array['volt']       <i>array</i>            Options when using volt template. OPTIONAL</li>
     *      <ul>
     *          <li>['path']            <i>string</i>           A writable path where the compiled PHP templates
     *                                                          will be placed.</li>
     *          <li>['extension']       <i>string</i>           An extension appended to the compiled PHP file</li>
     *          <li>['hierarchical']    <i>bool</i>             Set compiled hierarchical. Default is FALSE</li>
     *          <li>['separator']       <i>string</i>           Volt replaces the directory separators / and \ by
     *                                                          this separator in order to create a single file
     *                                                          in the compiled directory. Effect when hierarchical
     *                                                          is FALSE</li>
     *      </ul>
     * </ul>
     * @param array $options (See above)
     * @throws Exception\InvalidArgumentException
     */
    public function __construct(array $options)
    {
        if (!isset($options['di']) || !$options['di'] instanceof Di || !isset($options['views_dir'])) {
            throw new Exception\InvalidArgumentException(sprintf('Invalid options for %s', __CLASS__));
        }

        parent::__construct();
        $this->setDI($options['di']);
        $this->setViewsDir($options['views_dir']);
        $engines = [
            '.phtml' => Engine\Php::class,
            '.volt'  => $this->setVoltEngine($options['di'], isset($options['volt']) ? $options['volt'] : [])
        ];
        if (isset($options['engines'])) {
            $engines = $options['engines'];
        }
        $this->registerEngines($engines);
    }

    /**
     * Set complied path base on template path and config options
     *
     * @param string $templatePath template view path
     * @param array $options config for volt engine
     */
    private function setCompiledPath($templatePath, $options)
    {
        $viewsDir = $this->getViewsDir();

        $relativeViewFile = str_replace($viewsDir, '', $templatePath);
        if (!isset($options['hierarchical']) || !$options['hierarchical']) {
            $separator    = isset($options['separator']) ? $options['separator'] : '__';
            $compliedPath = str_replace(DIRECTORY_SEPARATOR, $separator, $relativeViewFile);
        } else {
            $relativeViewPath = substr($relativeViewFile, 0, strrpos($relativeViewFile, DIRECTORY_SEPARATOR));
            $absolutedPath    = $options['path'] . $relativeViewPath;
            if (!is_dir($absolutedPath)) {
                mkdir($absolutedPath, 0755, true);
            }

            if (!is_writable($absolutedPath)) {
                throw new Exception\RuntimeException(sprintf('Cannot write compile view to "%s"', $absolutedPath));
            }
            $compliedPath = $relativeViewFile;
        }

        $compliedPath = $options['path'] . $compliedPath;
        $compliedExt  = isset($options['extension']) ? $options['extension'] : '.php';
        $compliedPath .= $compliedExt;

        return $compliedPath;
    }

    /**
     * Set volt engine with options
     *
     * @param Di $di
     * @param array $options
     * @return \Phalcon\Mvc\View\Engine\Volt
     */
    private function setVoltEngine(Di $di, array $options = [])
    {
        $result = [];
        if (!isset($options['path'])) {
            $options['path'] = $this->getViewsDir();
        }
        $result['compiledPath'] = function ($templatePath) use ($options) {
            return $this->setCompiledPath($templatePath, $options);
        };

        $engine = new Engine\Volt($this, $di);
        if (!empty($result)) {
            $engine->setOptions($result);
        }
        return $engine;
    }
}
