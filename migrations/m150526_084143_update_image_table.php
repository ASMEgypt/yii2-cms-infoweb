<?php

use yii\db\Migration;

class m150526_084143_update_image_table extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('image', 'isMain');
        $this->addColumn('image', 'isMain', $this->integer()->defaultValue('0'));
    }

    public function safeDown()
    {
        $this->dropColumn('image', 'isMain');
        $this->alterColumn('image', 'isMain', $this->integer(3)->unsigned()->defaultValue('1'));
    }
}
