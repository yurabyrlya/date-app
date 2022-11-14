<?php

namespace App\Models;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;
use stdClass;

class Swipe
{
    private static $table = 'swipes';

    /**
     * @var Manager $conn
     */
    private static $conn;



    /**
     * @param Manager $connection
     * @param $data
     * @return array
     */
    public static function swipe(Manager $connection, $data) : array {

        self::$conn = $connection;

        if (!self::$conn->schema()->hasTable(self::$table)) {
            self::createSwipeTable();
        }
        $connection->table('swipes')->insert($data);

        return $data;
    }

    /**
     * @param $db
     * @param stdClass $user
     * @param stdClass $profile
     * @return bool
     */
    public static function isMatch(Manager $db, stdClass $user, stdClass $profile): bool {
           return (bool) $db->table('swipes')->where([
                ['user_id', '=', $profile->id],
                ['profile_id', '=', $user->id],
                ['preference', '=', true]
           ])->first();
    }

    /**
     * @param Manager $db
     * @param $user
     * @param $filters
     * @return stdClass
     */
    public static function getProfile(Manager $db, $user, $filters): ?stdClass{

        $userSwipes = $db->table('swipes')
            ->select(['profile_id'])
            ->where('user_id', '=', $user->id)
            ->get()
            ->pluck('profile_id')
            ->toArray();

        $profiles = $db->table('users')
            ->select(['id','name', 'age', 'gender'])
            ->whereNotIn('id', $userSwipes)
            ->get();

        foreach ($profiles as $profile){
            if ($profile->age >= $filters['from'] && $profile->age <= $filters['to'] ){
                return $profile;
            }
        }
        return null;
    }
    /**
     * @return void
     */
    private static function createSwipeTable(): void {

        self::$conn->schema()
            ->create(self::$table, function (Blueprint $table){
                $table->integerIncrements('id')->index();
                $table->integer('user_id')->unsigned();
                $table->integer('profile_id')->unsigned();;
                $table->boolean('preference');

                $table->foreign('user_id')
                    ->on('users')
                    ->references('id')
                    ->cascadeOnDelete();

                $table->foreign('profile_id')
                    ->on('users')
                    ->references('id')
                    ->cascadeOnDelete();
            });
    }

}