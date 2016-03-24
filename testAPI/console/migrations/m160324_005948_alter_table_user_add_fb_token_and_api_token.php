<?php

use yii\db\Migration;

class m160324_005948_alter_table_user_add_fb_token_and_api_token extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'fb_token', $this->string(255));
        $this->addColumn('user', 'api_token', $this->string(255));
    }

    public function down()
    {
        $this->dropColumn('user', 'fb_token');
        $this->dropColumn('user', 'api_token');
    }
}
