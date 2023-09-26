<?php

namespace App\Services;

use App\Models\Email;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class VerifaliaService
{
  protected Client $client;
  protected string $auth;
  protected string $url = 'https://api.verifalia.com/v2.4/';

  protected static string $VALID_EMAILS_TABLE = 'emails';

  public function __construct()
  {
    $this->client = new Client();

    try {
      // credentials
      $credentials = config('verifalia');
      $username = $credentials['username'];
      $password = $credentials['password'];

      // login
      $response = $this->client->post($this->url . 'auth/tokens', [
        'json' => ['username' => $username, 'password' => $password]
      ]);

      // sets the aut key
      $this->auth = 'Bearer ' . json_decode($response->getBody(), true)['accessToken'];
    } catch (\Exception $e) {
      $this->auth = '';
    }
  }

  /**
   * Verifies an email address using Verifalia's api.
   */
  static function emailIsValid(string $email): bool
  {
    $verifalia = new self();
    return $verifalia->verifyEmail($email);
  }


  /**
   * If successfully logged in with Verifalia, and there are free credits available verifies the email address.
   */
  public function verifyEmail(string $email): bool
  {
    if ($this->emailWasAlreadyValidated($email)) return true;
    // If login failed, or there are no free credits skip validation and assume email si valid.
    if (!$this->auth) return true;
    if (!$this->hasCredits()) return true;

    $payload = [
      'entries' => [
        ['inputData' => $email],
      ]
    ];

    try {
      // calls the api to perform validation
      $response = $this->client->post($this->url . 'email-validations', [
        'headers' => [
          'Authorization' => $this->auth,
          'Content-Type' => 'application/json'
        ],
        'json' => $payload
      ]);

      // reads response.
      $id = json_decode($response->getBody(), true)['overview']['id'];
      $response = $this->client->get($this->url . "email-validations/$id/entries/0", ['headers' => ['Authorization' => $this->auth]]);
    } catch (\Exception) {
      return true;
    }

    $result = json_decode($response->getBody(), true)['data'][0]['classification'];

    $isValid = $result === 'Deliverable';

    if ($isValid) $this->logValidEmail($email);
    else $this->logIssue("Il form è stato inviato da una email non valida: $email");

    return $isValid;
  }


  /**
   * Checks if there are available credits.
   */
  private function hasCredits(): bool
  {
    try {
      $response = $this->client->get($this->url . 'credits/balance', ['headers' => ['Authorization' => $this->auth]]);
    } catch (\Exception) {
      return false;
    }

    $free_credits = (int) json_decode($response->getBody(), true)['freeCredits'];

    return $free_credits > 0;
  }


  /**
   * Checks in the database if the email was already validated.
   */
  private function emailWasAlreadyValidated(string $email): bool
  {
    return DB::table('emails')->where('email', '=', $email)->exists();
  }


  /**
   * Logs valid emails in the database to skip future validation.
   */
  private function logValidEmail(string $email)
  {
    DB::table('emails')->insert(['email' => $email, 'validated_at' => now()]);
  }


  private function logIssue(string $message)
  {
    DB::table('logs')->insert(['message' => $message, 'created_at' => now()]);
  }
}
