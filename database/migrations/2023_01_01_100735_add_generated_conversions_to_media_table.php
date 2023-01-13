<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!app()->runningUnitTests()) {
            if (!Schema::hasColumn('media', 'generated_conversions')) {
                Schema::table('media', function (Blueprint $table) {
                    $table->json('generated_conversions');
                });
            }

            Media::query()
                ->whereNull('generated_conversions')
                ->orWhere('generated_conversions', '')
                ->orWhereRaw("JSON_TYPE(generated_conversions) = 'NULL'")
                ->update([
                    'generated_conversions' => DB::raw('custom_properties="$.generated_conversions"'),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn('generated_conversions');
        });
    }
};
