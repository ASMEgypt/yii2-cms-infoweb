<?php

use yii\db\Schema;
use yii\db\Migration;

class m151118_084144_update_image_lang_table extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('image_lang', 'url');
        $this->addColumn('image_lang', 'link', $this->string()->notNull());
    }

    public function safeDown()
    {
        $this->dropColumn('image_lang', 'link');
        $this->addColumn('image_lang', 'url', $this->string()->notNull());
    }
}
