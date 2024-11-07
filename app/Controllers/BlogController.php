<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Predis\Client as RedisClient;

class BlogController extends Controller
{
    protected $redis;
    protected $cacheDuration = 3000; // Cache duration in seconds (e.g., 5 minutes)

    public function __construct()
    {
        // Initialize Redis connection
        $this->redis = new RedisClient([
            // 'scheme' => 'tcp',
            // 'host'   => 'redis-14624.c283.us-east-1-4.ec2.redns.redis-cloud.com',
            // 'port'   => 14624,
            // 'password' => 'E3808ySSX0GJPzFbg2WdVIIFjiFaFzmW',

            // using local redis. 
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 6379,
            'password' => '',
        ]);
    }

    // Display list of all blog posts
    public function index()
    {
        // Retrieve all keys that match a specific pattern (e.g., all keys)
        $keys = $this->redis->keys('*');

        // Iterate over each key and display its value
        foreach ($keys as $key) {
            $value = $this->redis->get($key); // Get the value for each key
            echo "Key: $key, Value: " . $value . "<br>";
        }

        $cacheKey = 'all_posts';
        
        try {
            // Check Redis for cached posts
            if ($this->redis->exists($cacheKey)) {
                $posts = json_decode($this->redis->get($cacheKey), true);
                echo "SHowing Data from Redis cache";
            } else {
                // Retrieve posts from MySQL if not cached
                $db = \Config\Database::connect();
                $query = $db->query("SELECT * FROM posts ORDER BY created_at DESC");
                $posts = $query->getResultArray();

                // Cache posts in Redis
                $this->redis->setex($cacheKey, $this->cacheDuration, json_encode($posts));
                echo "Data from MySQL, cached in Redis";
            }
        } catch (\Exception $e) {
            // Fallback to MySQL if Redis fails
            log_message('error', 'Redis error: ' . $e->getMessage());
            $db = \Config\Database::connect();
            $query = $db->query("SELECT * FROM posts ORDER BY created_at DESC");
            $posts = $query->getResultArray();
            echo "Showing Data from MYSQL, Redis can't be accessed";
        }

        return view('posts', ['posts' => $posts]);
    }

    // Display a single post by ID
    public function view($id)
    {
        $cacheKey = "post_$id";

        try {
            // Check Redis for cached post
            if ($this->redis->exists($cacheKey)) {
                $post = json_decode($this->redis->get($cacheKey), true);
                echo "Data from Redis cache";
            } else {
                // Retrieve post from MySQL if not cached
                $db = \Config\Database::connect();
                $query = $db->query("SELECT * FROM posts WHERE id = ?", [$id]);
                $post = $query->getRowArray();

                // Cache post in Redis
                $this->redis->setex($cacheKey, $this->cacheDuration, json_encode($post));
                echo "Data from MySQL, cached in Redis";
            }
        } catch (\Exception $e) {
            // Fallback to MySQL if Redis fails
            log_message('error', 'Redis error: ' . $e->getMessage());
            $db = \Config\Database::connect();
            $query = $db->query("SELECT * FROM posts WHERE id = ?", [$id]);
            $post = $query->getRowArray();
        }

        return view('post', ['post' => $post]);
    }

    // Clear Redis cache when a post is updated
    public function updatePost($id, $data)
    {
        $db = \Config\Database::connect();
        $db->table('posts')->update($data, ['id' => $id]);

        // Clear Redis cache for this post and all posts
        $this->redis->del("post_$id");
        $this->redis->del('all_posts');
    }
}
