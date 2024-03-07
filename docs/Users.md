## Authentication
User authentication can be accomplished using 3 methods

- **Local Authentication** (default)
- **Admad Authentication** (https://github.com/ADmad/cakephp-social-auth)
- **Telegram Authentication** 

### Local Authentication
has been removed in favour of Admad.

In order to use Admad, you need to install the plugin and configure it in the settings.php file.
Remember to run the migration for creating the users table and the social_profiles table.

If you want google authentication, you need to create a project in the google console and get the client_id and the client_secret and put them in the settings.php file.
```php  
  'Google' => [
    'appid' => 'XXX', //Get this parameters in google developer console > api e services > credentials > ID client OAuth 2.0 > angelcake
    'secret' => 'XXX',
  ],
```

## Telegram Authentication
In order to use telegram authentication, you need to create a bot and get the token.
Then you need to configure the settings.php file with the token and the chat_id of the admin.

```php  
  'Telegram' => [
    'BotToken' => "",
    'BotUsername' => "xxx_bikesquare_bot",
    //Utenti che sono autorizzati ad accedere se non uso il db
    //Importante il campo con il ruolo si chiama group_id
    'Users' => [
      'massimoi' => ['group_id' => ROLE_ADMIN, 'name' => 'Massimo'],
      'luciasavino' => ['group_id' => ROLE_ADMIN, 'name' => 'Lucia'],
    ]
  ],
```

