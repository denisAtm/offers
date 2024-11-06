<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%offers}}`.
 */
class m241106_103331_create_offers_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('offers', [
            'id' => $this->primaryKey(),
            'offer_name' => $this->string()->notNull(),
            'representative_email' => $this->string()->notNull()->unique(),
            'representative_phone' => $this->string()->null(),
            'date_added' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('offers');
    }
}
