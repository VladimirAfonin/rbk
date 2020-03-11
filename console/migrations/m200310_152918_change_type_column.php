<?php

use yii\db\Migration;

/**
 * Class m200310_152918_change_type_column
 */
class m200310_152918_change_type_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('news', 'date_parse', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('news', 'date_parse', $this->integer());
    }

}
