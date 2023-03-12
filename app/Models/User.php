<?php

namespace App\Models;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use Firebase\JWT\Key;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;
use Faker;
use Firebase\JWT\JWT;


class User
{
    private static $table = 'users';

    /**
     * @var Manager $conn
     */
    private static $conn;

    /**
     * @param Manager $connection
     * @return array
     */
    public static function generateUser(Manager $connection) : array {

        self::$conn = $connection;

        $faker = Faker\Factory::create();
        $newUser =  [
            'email' => $faker->email,
            'password' => 'secret',
            'name' => $faker->name,
            'gender' => rand(0,1) ? 'Male': 'Female',
            'age' => rand(18, 140)
        ];
         self::addRecord($newUser);

         return $newUser;
    }

    /**
     * @param array $record
     * @return void
     */
    private static function addRecord(array $record): void
    {
        if (!self::$conn->schema()->hasTable(self::$table)) {
            self::createUserTable();
        }

        self::$conn->getDatabaseManager()
            ->table('users')
            ->insert($record);
    }

    /**
     * @return void
     */
    private static function createUserTable(): void {

        self::$conn->schema()
            ->create(self::$table, function (Blueprint $table){
                $table->integerIncrements('id')->index();
                $table->string('email');
                $table->string('password');
                $table->string('name');
                $table->string('gender');
                $table->smallInteger('age');
                $table->text('token')->nullable();
                $table->bigInteger('expires')->nullable();
        });
    }

    /**
     * @param Manager $connection
     * @param array $credentials
     * @param string $key
     * @return array
     */
    public static function login(Manager $connection, array $credentials, string $key) : array {

       if (!isset($credentials['email']) || !isset($credentials['password'])){
           return [
               'message' =>'Missing required credentials',
               'status' => 401,
           ];
        }
        $user = $connection
            ->getDatabaseManager()
            ->table(self::$table)
            ->select(['id','name', 'email', 'gender', 'age'])
            ->where([
                ['email' ,'=', $credentials['email']],
                ['password', '=', $credentials['password']],
            ])->first();

        if (!$user) {
          return [
              'message' =>'invalid username or password',
              'status' => 401,
          ];
        }
        $iat = time();
        $exp = $iat + 60 * 60;
        $payload = [
          'user' => (array) $user,
          'iss' => 'http://localhost:8080/login', //issuer
          'aud' => 'http://localhost:8080', //audience
          'iat' => $iat, // time JWT was issued
          'exp' =>  $exp // time JWT expires
        ];

        $jwt = JWT::encode($payload, $key, 'HS512');
        //(array) JWT::decode($jwt, new Key($key, 'HS512'));
        $tokenData = [
            'token'=> $jwt,
            'expires'=> $exp,
        ];
         $connection
            ->getDatabaseManager()
            ->table(self::$table)
            ->where('id', '=', $user->id)
            ->update($tokenData);

        return $tokenData;
    }

    /**
     * @param Container $container
     * @param $jwt
     * @return bool
     * @throws DependencyException
     * @throws NotFoundException
     */
    public static function authenticated(Container $container, $jwt) : bool {

        try {
            $key = $container->get('settings')['appKey'];
            $tokenData = JWT::decode($jwt, new Key($key, 'HS512'));
        } catch (Exception $e) {
            return false;
        }

         return (bool) $container
            ->get('connection')
            ->getDatabaseManager()
            ->table(self::$table)
            ->find($tokenData->user->id);
    }

    /**
     * @param Container $container
     * @param $jwt
     * @return \stdClass
     * @throws DependencyException
     * @throws NotFoundException
     */
    public static function Auth(Container $container, $jwt) : \stdClass {

        $key = $container->get('settings')['appKey'];
        $tokenData = JWT::decode($jwt, new Key($key, 'HS512'));
        return $container
            ->get('connection')
            ->table('users')
            ->find($tokenData->user->id);
    }

}