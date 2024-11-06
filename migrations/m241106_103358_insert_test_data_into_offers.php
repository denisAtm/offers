<?php

use yii\db\Migration;

/**
 * Class m241106_103358_insert_test_data_into_offers
 */
class m241106_103358_insert_test_data_into_offers extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        for ($i = 1; $i <= 15; $i++) {
            $this->insert('offers', [
                'offer_name' => 'Оффер ' . $i,
                'representative_email' => 'email' . $i . '@example.com',
                'representative_phone' => '+123456789' . $i,
            ]);
        }
    }

    public function safeDown()
    {
        $this->truncateTable('offers');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241106_103358_insert_test_data_into_offers cannot be reverted.\n";

        return false;
    }
    */
}
