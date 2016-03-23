<?php

namespace mikk150\render;

use yii\di\Instance;
use yii\db\Query;
use yii\helpers\FileHelper;
use Yii;

/**
*
*/
class View extends \yii\base\View
{
    /**
     * @var \yii\db\Connection
     */
    public $db = 'db';

    public $viewTable = 'views';

    public $runtime = '@runtime/sqlRenderer';

    public function init()
    {
        parent::init();
        $this->db = Instance::ensure($this->db, 'yii\db\Connection');
    }

    protected function findViewRow($view, $context)
    {
        $query = (new Query())->select('*')->from($this->viewTable)->where('slug = :slug', ['slug' => $view])->one($this->db);
        return $query;
    }
    /**
     * Finds the view file based on the given view name.
     * @param string $view the view name or the path alias of the view file. Please refer to [[render()]]
     * on how to specify this parameter.
     * @param object $context the context to be assigned to the view and can later be accessed via [[context]]
     * in the view. If the context implements [[ViewContextInterface]], it may also be used to locate
     * the view file corresponding to a relative view name.
     * @return string the view file path. Note that the file may not exist.
     * determine the corresponding view file.
     */
    protected function findViewFile($view, $context = null)
    {
        $viewRow = $this->findViewRow($view, $context);
        
        $viewFile = $this->generateFileName($viewRow['slug'], $viewRow['hash']);
        if (sha1($viewRow['content']) !== $viewRow['hash']) {
            if (file_exists(Yii::getAlias($viewFile))) {
                unlink(Yii::getAlias($viewFile));
            }

            $viewFile = $this->generateFileName($viewRow['slug'], sha1($viewRow['content']));
        }
        
        if (!file_exists(Yii::getAlias($viewFile))) {

            FileHelper::createDirectory(Yii::getAlias($this->runtime));

            $fop = fopen(Yii::getAlias($viewFile), 'w');
            fwrite($fop, $viewRow['content']);
            fclose($fop);

            $this->db->createCommand()->update($this->viewTable, ['hash' => sha1($viewRow['content'])], ['id' => $viewRow['id']])->execute();
        }
        
        return parent::findViewFile($viewFile, $context);
    }

    public function generateFileName($slug, $sha)
    {
        $ext = pathinfo($slug, PATHINFO_EXTENSION);
        if (empty($ext)) {
            $ext = 'php';
        }

        return $this->runtime.'/'.$slug.$sha.'.'.$ext;
    }
}
