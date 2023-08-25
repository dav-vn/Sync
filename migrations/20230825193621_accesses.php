<?php

use Phpmig\Migration\Migration;
use Illuminate\Database\Capsule\Manager;

class Accesses extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        Manager::schema()->create('users', function ($table) {
            $table->id();
            $table->string('base_domain');
            $table->
            $table->string('refresh_token');

        });
    }
{
"31608991": {
"base_domain": "davvrtn.kommo.com",
"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImUyYTdhNTRjYWRmNzcwOGQ0OGFkYTY2Yjc5OWVjZmYxY2FjZDRhODY4YzYyYTRlZTIwYWE3NDU3NWY4NGVhMmFhYTZhZDBlMjE2ZDEyMzhiIn0.eyJhdWQiOiIyYmQ0YmEwZC0zMzgyLTRmZDktOWViOS02NjRjYzVkZjczNTUiLCJqdGkiOiJlMmE3YTU0Y2FkZjc3MDhkNDhhZGE2NmI3OTllY2ZmMWNhY2Q0YTg2OGM2MmE0ZWUyMGFhNzQ1NzVmODRlYTJhYWE2YWQwZTIxNmQxMjM4YiIsImlhdCI6MTY5Mjk1OTAzMiwibmJmIjoxNjkyOTU5MDMyLCJleHAiOjE2OTMwNDU0MzIsInN1YiI6Ijk5OTYxNzEiLCJncmFudF90eXBlIjoiIiwiYWNjb3VudF9pZCI6MzE2MDg5OTEsImJhc2VfZG9tYWluIjoia29tbW8uY29tIiwidmVyc2lvbiI6InYyIiwic2NvcGVzIjpbInB1c2hfbm90aWZpY2F0aW9ucyIsImZpbGVzIiwiY3JtIiwiZmlsZXNfZGVsZXRlIiwibm90aWZpY2F0aW9ucyJdfQ.iDHLrNwuqumT_P2ENh-ULBb2gerwLkY9Sna8eCM_U9wsYsZI_N5tE7lZ-IKkGqh-w3Ogz-myU_Hhntp8YGd4wNnJB7SOVInp2kBrRWqmnYry9fuPtKK0wJ7EZzkR3xOYCTzV_MOOjWwhajdsxIsWzJqvSFP3ZegML07EQlTEB2VZWR8ON4ST6c_RUAUgxN4Rl672e2Yv4VWgr5OLzyEx_FqC7gvFRu2IpXlj3TPml2kgQg7UHkUmGd4ozze0dMepM5lyr_hDNkiS4jityw_dE_AQhcbb77Ous1qAvFiu8wc2aLCknfa47EjVaFCxA_oXlcUZDxDgWDaXpBhfWG_Hww",
"refresh_token": "def50200adcb4b6e8760bffd517cd008748635f53ec32f47502cfbea5421a3b1bbd3d2f7226a2e180f2c80b5b057554e90520f9aa8317fc77341e6dabe2e78214b7d95adc06104bd90cdfab8c4914b4725a04825c77a0e9e25f2834a0397683eef1865924bbec61077ca57ee575d54d7fcdc42b3193e9d96313233430bfd2720f7747bf867f79ccabe6b240d28693d1bd0a3efe8dd3517110ab980902b0a47592694b11dcdeb06baef5cacbd05096ec693bb871c8903efdaebb2bbd4a5ebfccd3e814341babd3d1598c16a0a6a023bddbe078148a38b1118c7429e7877c2555e78b5fae1e8fbf4960d880c6a57ebbaa5c16910f5bad632d038b69352036f8a61326e2887ce52c5fb372335e62a1b7785d22ef89add113fe7cd41cd0e23c29e4ec477097504df0dd60183c25be24cd128e1911dfd4d50bf9a5d4ea953a6401506846d361f66fbfb61af3645bb262c2241f43b15aa188a2f870aae5c45360b720cf3229de3f41f040f3691fdbb348cf0f86162b1180409a5ccf3d9b26f7ec0577d4a194734f90637c6a30cbaf4bc9599352eaabbbd6ee39f442b1282784aaeb006edc76c9f4fec3a59f5a2e28d1dbb2fe67e8af7489a9ae8131e1148e4fcac54856579175f78157dd4923bfa2b6fb11da5ee15c498edb9ed7394c97a510404883bf93fb5b1cccf331aaffaa00da4adf4bd",
"expires": 1693045432
}
}

    /**
     * Undo the migration
     */
    public function down()
    {
        Manager::schema()->dropIfExists('integrations');
    }
}
