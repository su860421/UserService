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
        Schema::create('organiztions', function (Blueprint $table) {
            $table->ulid('id')->primary()->comment('主鍵 ULID');
            $table->string('name')->comment('部門名稱');
            $table->string('type')->nullable()->comment('部門類型');
            $table->foreignUlid('parent_id')->nullable()->constrained('organiztions')->nullOnDelete()->comment('上層部門');
            $table->foreignUlid('manager_user_id')->nullable()->constrained('users')->nullOnDelete()->comment('部門主管');
            $table->string('address')->nullable()->comment('地址');
            $table->string('phone')->nullable()->comment('電話');
            $table->string('email')->nullable()->comment('信箱');
            $table->decimal('monthly_budget', 12, 2)->nullable()->comment('月預算');
            $table->json('approval_settings')->nullable()->comment('審核設定');
            $table->json('settings')->nullable()->comment('組織設定');
            $table->string('cost_center_code')->nullable()->comment('成本中心代碼');
            $table->tinyInteger('status')->default(1)->comment('狀態：1=啟用(active)，0=停用(inactive)');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['parent_id', 'name'], 'uniq_parent_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organiztions');
    }
};
