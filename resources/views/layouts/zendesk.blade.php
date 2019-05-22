<?php
	// load Composer
/*require __DIR__.'/../../../vendor/autoload.php';

use Zendesk\API\HttpClient as ZendeskAPI;

$subdomain = "5ecr";
$username  = "ali@5e.cr"; // replace this with your registered email
$token     = "IozVFmXc4kbXOkeDl60AtN47TrzcVQalrOwXrR4P"; // replace this with your token

$client = new ZendeskAPI($subdomain);
$client->setAuth('basic', ['username' => $username, 'token' => $token]);

// Get all tickets
$tickets = $client->tickets()->findAll();
// var_dump($tickets);
$all = json_decode(json_encode($tickets), true);
echo 'Zendes requester_id: ' . $all['tickets'][0]['requester_id'].'<br>';
echo 'Zendes submitter_id: ' . $all['tickets'][0]['submitter_id'];*/
// var_dump($all);
//print_r($tickets);

// Get all tickets regarding a specific user.
//$tickets = $client->users($requesterId)->tickets()->requested();
//print_r($tickets);

// Create a new ticket
/*$newTicket = $client->tickets()->create([
    'subject'  => 'The quick brown fox jumps over the lazy dog',
    'comment'  => [
        'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, ' .
                  'sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
    ],
    'priority' => 'normal'
]);
print_r($newTicket);*/

/*// Update a ticket
$client->tickets()->update(123,[
    'priority' => 'high'
]);

// Delete a ticket
$client->tickets()->delete(123);

// Get all users
$users = $client->users()->findAll();
print_r($users);*/
?>