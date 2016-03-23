<?php

namespace mikk150\render;

use yii\di\Instance;

/**
*
*/
class View extends yii\base\View
{
    public $db = 'db';

    public $viewTable = 'views';

    public function init()
    {
        parent::init();
        $this->db = Instance::ensure($this->db, 'yii\db\Connection');
    }

    public function render($view, $params = [], $context = null)
    {
        $viewRow = $this->findViewRow($view, $context);
        return $this->renderViewRow($viewRow, $params, $context);
    }

    protected function findViewRow($view, $context)
    {
        
    }
}
