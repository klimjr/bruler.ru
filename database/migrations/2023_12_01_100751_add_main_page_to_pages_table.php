<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      $sql = <<<SQL
INSERT INTO `pages` (`id`, `name`, `seo_fields`, `type`, `seo_title`, `deleted_at`, `created_at`, `updated_at`, `payload`) VALUES (NULL, 'Главная', '[{\"meta_tag\":null,\"content\":null}]', 'main_page', NULL, NULL, '2023-12-01 09:07:13', '2023-12-01 09:24:41', '{\"video\":\"videos\\/01HGJAFM88QTJ6P8QE022ZQFNY.mp4\",\"video_mobile\":\"videos\\/01HGJB3KCSW14Y4WK2QA0F0T2F.mp4\"}')
SQL;

      DB::unprepared($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
