<?php

use yii\db\Migration;

/**
 * Class m200310_115226_add_date_parse_column
 */
class m200310_115226_add_date_parse_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('news', 'date_parse', $this->integer()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('news', 'date_parse');
    }

}
