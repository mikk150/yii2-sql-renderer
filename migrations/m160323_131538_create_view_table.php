<?php

use yii\db\Migration;

class m160323_131538_create_view_table extends Migration
{
    public function up()
    {
        $this->createTable('views', [
            'id' => $this->primaryKey(),
            'slug' => $this->string()->notNull(),
            'content' => $this->text(),
            'hash' => $this->string(64)
        ]);
    }

    public function down()
    {
        $this->dropTable('views');
    }
}
