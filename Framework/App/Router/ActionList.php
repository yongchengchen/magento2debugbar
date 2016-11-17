<?php

namespace Yong\Magento2DebugBar\Framework\App\Router;

use Magento\Framework\App\Router\ActionList as BaseActionList;

class ActionList extends BaseActionList
{
    private $tried_fullpaths = [];

    public function clearTriedFullPaths() {
        $this->tried_fullpaths = [];
    }

    public function getTriedFullPaths() {
        return $this->tried_fullpaths;
    }
    /**
     * Retrieve action class
     *
     * @param string $module
     * @param string $area
     * @param string $namespace
     * @param string $action
     * @return null|string
     */
    public function get($module, $area, $namespace, $action)
    {
        if ($area) {
            $area = '\\' . $area;
        }
        if (strpos($namespace, self::NOT_ALLOWED_IN_NAMESPACE_PATH) !== false) {
            return null;
        }
        if (in_array(strtolower($action), $this->reservedWords)) {
            $action .= 'action';
        }
        $fullPath = str_replace(
            '_',
            '\\',
            strtolower(
                $module . '\\controller' . $area . '\\' . $namespace . '\\' . $action
            )
        );

        if (isset($this->actions[$fullPath])) {
            $result = is_subclass_of($this->actions[$fullPath], $this->actionInterface) ? $this->actions[$fullPath] : null;
            if ($result === null) {
                $this->tried_fullpaths[] = sprintf('%s not a subclass of %s', $fullPath, $this->actionInterface);
            }
            return $result;
        }

        $this->tried_fullpaths[] = $fullPath . ' not found';
        return null;
    }
}
