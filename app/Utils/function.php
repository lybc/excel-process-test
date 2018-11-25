<?php

if (! function_exists('generate_test_data')) {
    function generate_test_data()
    {
        $faker = \Faker\Factory::create('zh_CN');
        $result = [];
        for ($i = 0; $i < 100000; $i++) {
            $arr = [
                'name' => $faker->name,
                'age' =>$faker->randomNumber(),
                'email' => $faker->email,
                'address' => $faker->address,
                'company' => $faker->company,
                'country' => $faker->country,
                'birthday' => $faker->date(),
                'city' => $faker->city,
                'creditCardNumber' => $faker->creditCardNumber,
                'street' => $faker->streetName,
                'postCode' => $faker->postcode,
            ];
            $result[] = $arr;
        }
        return $result;
    }
}
