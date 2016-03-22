<?php

use yii\db\Schema;
use yii\db\Migration;

class m160321_233126_alter_table_user extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'first_name', $this->string(255));
        $this->addColumn('user', 'last_name', $this->string(255));
        $this->addColumn('user', 'image', $this->string(255));
        $this->addColumn('user', 'lat', $this->string(255));
        $this->addColumn('user', 'lon', $this->string(255));
        $this->addColumn('user', 'city', $this->string(255));
        $this->addColumn('user', 'country', $this->string(255));
        $this->addColumn('user', 'gender', $this->string(255));
    }

    public function down()
    {
        $this->dropColumn('user', 'first_name');
        $this->dropColumn('user', 'last_name');
        $this->dropColumn('user', 'image');
        $this->dropColumn('user', 'lat');
        $this->dropColumn('user', 'lon');
        $this->dropColumn('user', 'city');
        $this->dropColumn('user', 'country');
        $this->dropColumn('user', 'gender');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
