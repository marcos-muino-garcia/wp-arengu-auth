# Arengu Auth Wordpress plugin
This module enables custom signup, login and passwordless endpoints to interact with Wordpress authentication system from Arengu.

## Available endpoints

1. [Signup](#signup)
2. [Login](#login)
3. [Passwordless](#passwordless)
4. [Check existing email](#check-existing-email)

### Authentication

This module uses an API key to authenticate requests. You can view and manage your API key under your module settings. This API key **allows to impersonate any user in your site, so you must keep it secret and do not share it in publicly accessible areas such as GitHub, client-side code, and so forth.**

Authentication to the API is performed via bearer authentication:

```
Authorization: Bearer YOUR_API_KEY
```

### Signup

Sign up users with email and password or just with an email (passwordless signup).

`POST` **/wp-json/arengu/signup**

#### Request parameters

| Parameter | Type | Description |
| ------ | ------ | ------ |
| email _(required)_| [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | The user email. |
| password _(optional)_ | [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | The user plain password. If you don't provide a password a random one will be generated. This is useful if you want to use passwordless flows. |
| meta _(optional)_ | [Object](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object) | An object with key-value pairs with user meta data. |
| remember _(optional)_ | [Boolean](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Boolean) | The default the cookie is kept without remembering is two days. When remember is set, the cookies will be kept for 14 days (Default value is `false`). |
| secure _(optional)_ | [Boolean](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Boolean) | Whether the auth cookie should only be sent over HTTPS (Default value is `true`). |

#### Request sample

```json
{
  "email": "jane.doe@arengu.com",
  "password": "foobar",
  "meta": {
    "company": "Arengu",
    "city": "A Coruña"
  },
  "remember": true
}
```

#### Response headers sample

```curl
Set-Cookie: wordpress_logged_in_37d007a.....=jane.doe%40arengu.com%7C1591208581%7C2i6gINuPt1uzilqyJm4....; path=/; HttpOnly
```

#### Response body sample

```json
{
  "user": {
    "id": "1",
    "email": "jane.doe@arengu.com"
  },
}
```

#### Error codes

| Error code | Description |
| ------ | ------ |
| existing_user_login | This email is already being used by another user. |
| empty_user_login | You are not sending an email addres in the request. |

### Login

Log in users with email and password.

`POST` **/wp-json/arengu/auth**

#### Request parameters

| Parameter | Type | Description |
| ------ | ------ | ------ |
| email _(required)_| [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | The user email you want to sign up. |
| password _(required)_ | [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | Query selector or DOM element that the form will be appended to. |
| remember _(optional)_ | [Boolean](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Boolean) | The default the cookie is kept without remembering is two days. When remember is set, the cookies will be kept for 14 days (Default value is `false`). |
| secure _(optional)_ | [Boolean](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Boolean) | Whether the auth cookie should only be sent over HTTPS (Default value is `true`). |

#### Request sample

```json
{
  "email": "jane.doe@arengu.com",
  "password": "foobar"
}
```

#### Response headers sample

```curl
Set-Cookie: wordpress_logged_in_37d007a.....=jane.doe%40arengu.com%7C1591208581%7C2i6gINuPt1uzilqyJm4....; path=/; HttpOnly
```

#### Response body sample

```json
{
  "user": {
    "id": "1",
    "email": "jane.doe@arengu.com"
  },
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

`POST` **/wp-json/arengu/passwordless/auth**

⚠️ **IMPORTANT** ⚠️ This endpoint is made to be used adding one authentication factor to verify the user identity (eg. one-time password via email or SMS, social login, etc).

#### Request parameters

| Parameter | Type | Description |
| ------ | ------ | ------ |
| email _(required)_| [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | The user email you want to authenticate. |
| remember _(optional)_ | [Boolean](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Boolean) | The default the cookie is kept without remembering is two days. When remember is set, the cookies will be kept for 14 days (Default value is `false`). |
| secure _(optional)_ | [Boolean](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Boolean) | Whether the auth cookie should only be sent over HTTPS (Default value is `true`). |

#### Request sample

```json
{
  "email": "jane.doe@arengu.com"
}
```

#### Response headers sample

```curl
Set-Cookie: wordpress_logged_in_37d007a.....=jane.doe%40arengu.com%7C1591208581%7C2i6gINuPt1uzilqyJm4....; path=/; HttpOnly
```

#### Response body sample

```json
{
  "user": {
    "id": "1",
    "email": "jane.doe@arengu.com"
  },
}
```

#### Error codes

| Error code | Description |
| ------ | ------ |
| email_not_exists | This email is not registered in your database. |

### Check existing email

Check if an email exists in your database.

`POST` **/wp-json/arengu/checkEmail**

#### Request parameters

| Parameter | Type | Description |
| ------ | ------ | ------ |
| email _(required)_| [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | The user email. |

#### Request sample

```json
{
  "email": "jane.doe@arengu.com"
}
```

#### Response sample

```json
{
  "email_exists": true
}
```

#### Error codes

| Error code | Description |
| ------ | ------ |
| invalid_email | You are providing an invalid or no email address. |

## Embed methods

### Method 1: Using a shortcode tag (Recommended)
Place the following shortcode tag where you want your form to appear:

```
[arengu-form id="YOUR_FORM_ID"]
```

You have to replace `YOUR_FORM_ID` with your **Form ID**, which you can find in your form settings or share page.

### Method 2: Using an HTML tag
Place the following HTML tag where you want your form to appear:

```html
<div data-arengu-form-id="YOUR_FORM_ID"></div>
```

### Method 3:** Calling our `embed` method

Our SDK has a method to embed your form inside any element.

`embed` method definition:
```
ArenguForms.embed(formId, selector);
```
The `embed` call has the following fields:

| Parameter | Type | Description |
| ------ | ------ | ------ |
| formId _(required)_| [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | The **Form ID** of your form. You can find it in your form settings or share page. |
| selector _(required)_ | [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String)\|[Element](https://developer.mozilla.org/en-US/docs/Web/API/Element) | Query selector or DOM element that the form will be appended to. |

Example using the query selector:

```javascript
ArenguForms.embed('5073697614331904', '.form-container');
```

That snippet will embed the form with ID `5073697614331904` into the element with `.form-container` class.

Another example using the element directly:

```javascript
const container = document.querySelector('.form-container');
ArenguForms.embed('5073697614331904', container);
```
In this case, the snippet gets a reference to the element and passes it directly to the `embed()` method.