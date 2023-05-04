<?php

use Phinx\Migration\AbstractMigration;

class CreateMessagesTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('messages');
        $table->addColumn('message', 'string', ['limit' => 300, 'null' => false])
              ->addColumn('created_at', 'timestamp', ['timezone' => true, 'null' => false, 'default' => 'CURRENT_TIMESTAMP'])
              ->create();
    }
}