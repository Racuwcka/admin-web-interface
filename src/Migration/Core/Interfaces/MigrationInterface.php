<?php

namespace Migration\Core\Interfaces;
interface MigrationInterface
{
    public function up(): bool;
    public function down(): bool;
}