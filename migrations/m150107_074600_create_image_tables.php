<?php

use yii\db\Schema;
use yii\db\Migration;

class m150107_074600_create_image_tables extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // Drop image table
        if ($this->db->schema->getTableSchema('image', true)) {
            $this->dropTable('image');
        }

        $this->createTable('{{%image}}', [
            'id'         => $this->primaryKey(),
            'name'       => $this->string()->notNull(),
            'filePath'   => $this->string(400)->notNull(),
            'itemId'     => $this->string()->notNull(),
            'isMain'     => $this->integer(3)->unsigned()->notNull()->defaultValue('1'),
            'modelName'  => $this->string()->notNull(),
            'urlAlias'   => $this->string()->notNull(),
            'position'   => $this->integer()->unsigned()->notNull(),
            'active'     => $this->integer(3)->unsigned()->notNull()->defaultValue('1'),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned()->notNull(),
        ], $tableOptions);

        $this->createIndex('itemId', '{{%image}}', 'itemId');

        // Create 'image_lang' table
        $this->createTable('{{%image_lang}}', [
            'image_id'    => $this->integer()->notNull(),
            'language'    => $this->string(10)->notNull(),
            'alt'         => $this->string()->notNull(),
            'title'       => $this->string()->notNull(),
            'subtitle'    => Schema::TYPE_STRING . ' NOT NULL',
            'description' => Schema::TYPE_TEXT . ' NOT NULL',
            'url'         => $this->string()->notNull(),
            'created_at'  => $this->integer()->unsigned()->notNull(),
            'updated_at'  => $this->integer()->unsigned()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('image_lang_image_id_language', '{{%image_lang}}', ['image_id', 'language']);
        $this->createIndex('image_lang_language_i', '{{%image_lang}}', 'language');
        $this->addForeignKey('FK_IMAGE_LANG_IMAGE_ID', '{{%image_lang}}', 'image_id', '{{%image}}', 'id', 'CASCADE', 'NO ACTION');
    }

    public function safeDown()
    {
        $this->dropTable('image_lang');
        $this->dropTable('image');
    }
}
