<?php

// namespace Config;

// use CodeIgniter\Config\BaseConfig;
// use Predis\Client as PredisClient; // Use Predis if you installed it via Composer

// class Redis extends BaseConfig
// {
//     private $host = 'redis-14624.c283.us-east-1-4.ec2.redns.redis-cloud.com';       // Redis server host
//     private $port = 14624;              // Default Redis port
//     private $password = 'E3808ySSX0GJPzFbg2WdVIIFjiFaFzmW';          // Redis password, if set
//     private $timeout = 0;              // Connection timeout (0 = no timeout)

//     public function initialize()
//     {
//         return new PredisClient([
//             'scheme' => 'tcp',
//             'host'   => $this->host,
//             'port'   => $this->port,
//             'password' => $this->password,
//             'timeout' => $this->timeout,
//         ]);
//     }
// }




namespace Config;

use CodeIgniter\Config\BaseConfig;
use Predis\Client as PredisClient;

class Redis extends BaseConfig
{
    private $host = '127.0.0.1';       // Localhost for local Redis
    private $port = 6379;              // Default Redis port for local setup
    private $password = null;          // Leave null if no password is set
    private $timeout = 0;              // Connection timeout (0 = no timeout)

    public function initialize()
    {
        return new PredisClient([
            'scheme' => 'tcp',
            'host'   => $this->host,
            'port'   => $this->port,
            'password' => $this->password,
            'timeout' => $this->timeout,
        ]);
    }
}
