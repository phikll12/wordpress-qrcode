<?php

// Set required headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

/**
 * Example: First
 *
 * Get JSON data from JSON file and retun as JSON response
 */

// Get JSON data from JSON file
$json = file_get_contents('response.json');

// Output, response
echo $json;

/** =. =.=. =.=. =.=. =.=. =.=. =.=. =.=. =.=. =.=. =.  */

/**
 * Example: Second
 *
 * Build JSON data from PHP array and retun as JSON response
 */

// Or build JSON data from array (PHP)
$json_var = [
  'hashtag' => 'HealthMatters',
  'id' => '072b3d65-9168-49fd-a1c1-a4700fc017e0',
  'sentiment' => [
    'negative' => 44,
    'positive' => 56,
  ],
  'total' => '3400',
  'users' => [
    [
      'profile_image_url' => 'http://a2.twimg.com/profile_images/1285770264/PGP_normal.jpg',
      'screen_name' => 'rayalrumbel',
      'text' => 'Tweet (A), #HealthMatters because life is cool :) We love this life and want to spend more.',
      'timestamp' => '{{$timestamp}}',
    ],
    [
      'profile_image_url' => 'http://a2.twimg.com/profile_images/1285770264/PGP_normal.jpg',
      'screen_name' => 'mikedingdong',
      'text' => 'Tweet (B), #HealthMatters because life is cool :) We love this life and want to spend more.',
      'timestamp' => '{{$timestamp}}',
    ],
    [
      'profile_image_url' => 'http://a2.twimg.com/profile_images/1285770264/PGP_normal.jpg',
      'screen_name' => 'ScottMili',
      'text' => 'Tweet (C), #HealthMatters because life is cool :) We love this life and want to spend more.',
      'timestamp' => '{{$timestamp}}',
    ],
    [
      'profile_image_url' => 'http://a2.twimg.com/profile_images/1285770264/PGP_normal.jpg',
      'screen_name' => 'yogibawa',
      'text' => 'Tweet (D), #HealthMatters because life is cool :) We love this life and want to spend more.',
      'timestamp' => '{{$timestamp}}',
    ],
  ],
];

// Output, response
echo json_encode($json_var);