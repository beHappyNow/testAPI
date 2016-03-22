<?php

use yii\db\Migration;

class m160322_133537_add_access_token_to_user extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'access_token', $this->string(255));
    }

    public function down()
    {
        $this->dropColumn('user', 'access_token');
    }
}
