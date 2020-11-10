# Arengu Auth Wordpress plugin
This module enables custom signup, login and passwordless endpoints to interact with WordPress's authentication system from [Arengu flows](https://www.arengu.com/flows/).

## Installation

1. Upload the plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Go to "Arengu" menu to see the info you need to connect a flow to WordPress.

## Available endpoints

- [Private endpoints](#private-endpoints)
  1. [Sign up](#sign-up)
  2. [Log in](#log-in)
  3. [Passwordless](#passwordless)
  5. [Check existing email](#check-existing-email)
- [Public endpoints](#public-endpoints)
  1. [Log in with JWT](#log-in-with-jwt)

### Private endpoints

The private part of the API is protected by an API key. You can view and manage your API key under your plugin settings, in the WordPress admin panel.

> **Warning:** This API key **allows to impersonate any user in your blog, so you must keep it secret and do not share it in publicly accessible areas such as GitHub, client-side code, and so forth.**

Authentication to the API is performed via `Authorization` header with `Bearer` schema:

```
Authorization: Bearer YOUR_API_KEY
```

### Signup

Sign up users with email and password or just with an email (passwordless signup).

```
POST /index.php?rest_route=/arengu_auth/signup
Content-Type: application/json
```

#### Request parameters

| Parameter | Type | Description |
| ------ | ------ | ------ |
| email _(required)_| [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | The user email. |
| password _(optional)_ | [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | The user plain password. If you don't provide a password a random one will be generated. This is useful if you want to use passwordless flows. |
| first_name _(optional)_ | [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | The user's first name. |
| last_name _(optional)_ | [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | The user's last name. |
| meta _(optional)_ | [Object](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object) | An object with key-value pairs with user meta data. |
| expires_in _(optional)_ | [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Number) | Number of seconds that the JWT will be valid. By default it's 300 (5 minutes). |
| redirect_uri _(optional)_ | [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | The URL where you want to redirect the user after signing him up when you send him to the JWT verification endpoint. By default it's the user account page. |

#### Operation example

```
> POST /index.php?rest_route=/arengu_auth/signup
> Content-Type: application/json
{
  "email": "jane.doe@arengu.com",
  "password": "foobar",
  "meta": {
    "company": "Arengu",
    "city": "A Coruña"
  },
  "first_name": "Jane",
  "last_name": "Doe"
}

< HTTP/1.1 200 OK
< Content-Type: application/json
{
  "user": {
    "id": 1,
    "email": "jane.doe@arengu.com",
    "first_name": "Jane",
    "last_name": "Doe"
  },
  "token": "...",
  "login_url": "..."
}
```

#### Error codes

| Error code | Description |
| ------ | ------ |
| existing_user_login | This email is already being used by another user. |
| empty_user_login | You are not sending an email address in the request. |

### Login

Log in users with email and password.

```
POST /index.php?rest_route=/arengu_auth/login_password
Content-Type: application/json
```

#### Request parameters

| Parameter | Type | Description |
| ------ | ------ | ------ |
| email _(required)_| [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | The user email you want to sign up. |
| password _(required)_ | [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | Query selector or DOM element that the form will be appended to. |
| expires_in _(optional)_ | [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Number) | Number of seconds that the JWT will be valid. By default it's 300 (5 minutes). |
| redirect_uri _(optional)_ | [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | The URL where you want to redirect the user after logging him in when you send him to the JWT verification endpoint. By default it's the user account page. |

```
> POST /index.php?rest_route=/arengu_auth/login_password
> Content-Type: application/json
{
  "email": "jane.doe@arengu.com",
  "password": "foobar",
}

< HTTP/1.1 200 OK
< Content-Type: application/json
{
  "user": {
    "id": 1,
    "email": "jane.doe@arengu.com",
    "first_name": "Jane",
    "last_name": "Doe"
  },
  "token": "...",
  "login_url": "..."
}
```

#### Error codes

| Error code | Description |
| ------ | ------ |
| empty_username | You are not sending an email address in the request. |
| empty_password | You are sending an empty or no password in the request. |
| invalid_email | You are providing an invalid email address that is not registered. |
| incorrect_password | You are providing an invalid password. |

### Passwordless

Authenticate users without password.

```
POST /index.php?rest_route=/arengu_auth/passwordless_login
Content-Type: application/json
```

> **Warning:** This endpoint was designed to be invoked once the user identity is verified using, at least, one authentication factor (eg. one-time password via email or SMS, social login, etc).

#### Request parameters

| Parameter | Type | Description |
| ------ | ------ | ------ |
| email _(required)_| [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | The user email you want to authenticate. |
| expires_in _(optional)_ | [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Number) | Number of seconds that the JWT will be valid. By default it's 300 (5 minutes). |
| redirect_uri _(optional)_ | [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | The URL where you want to redirect the user after logging him in when you send him to the JWT verification endpoint. By default it's the user account page. |

##### Operation example

```
> POST /index.php?rest_route=/arengu_auth/passwordless_login
> Content-Type: application/json
{
  "email": "jane.doe@arengu.com"
}

< HTTP/1.1 200 OK
< Content-Type: application/json
{
  "user": {
    "id": 1,
    "email": "jane.doe@arengu.com",
    "first_name": "Jane",
    "last_name": "Doe"
  },
  "token": "...",
  "login_url": "..."
}
```

#### Error codes

| Error code | Description |
| ------ | ------ |
| email_not_exists | This email is not registered in your database. |

### Check existing email

Check if an email exists in your database.

```
POST /index.php?rest_route=/arengu_auth/check_email
Content-Type: application/json
```

#### Request parameters

| Parameter | Type | Description |
| ------ | ------ | ------ |
| email _(required)_| [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | The user email. |


##### Operation example
```
> POST /index.php?rest_route=/arengu_auth/check_email
> Content-Type: application/json
{
  "email": "jane.doe@arengu.com"
}

< HTTP/1.1 200 OK
< Content-Type: application/json
{
  "email_exists": true
}
```

#### Error codes

| Error code | Description |
| ------ | ------ |
| invalid_email | You are providing an invalid or no email address. |


#### Log in with JWT

Make a user to be logged in by redirecting him to this URL with a signed JWT that you previously received as a response in a signup or login request.

`GET` **/index.php?rest_route=/arengu_auth/login_jwt**

##### URL parameters

| Parameter | Type | Description |
| ------ | ------ | ------ |
| token _(required)_| [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | A signed JSON web token (JWT), containing `sub` (the user ID), `email` (the user email) and optionally `redirect_uri` with the absolute or relative URL the user will be redirected after the login. If the latter is not specified, the user will be redirected to the home page. |

## Embed methods

### Recommended method: use a shortcode tag in a post or page
Place the following shortcode tag where you want your form to appear:

```
[arengu-form id="YOUR_FORM_ID"]
```

You have to replace `YOUR_FORM_ID` with your **Form ID**, which you can find in your form settings or share page.

### Advanced method: use our SDK directly
You can read more about this in [the repository for our SDK](https://github.com/arengu/forms-js-sdk).