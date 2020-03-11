# Arengu Forms Wordpress plugin
This plugin allows you to easily embed forms and enables user sign up and sign in capabilities with custom endpoints in WordPress.

## **Available endpoints**

### **Signup**

Sign up users with email and password or just with an email (passwordless signup).

`POST` **/wp-json/arengu/signup**

#### Request parameters

| Parameter | Type | Description |
| ------ | ------ | ------ |
| email _(required)_| [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | The user email. |
| password | [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | The user plain password. If you don't provide a password a random one will be generated. This is useful if you want to use passwordless flows. |
| meta | [Object](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object) | An object with key-value pairs with user meta data. |

#### Request sample

```json
{
  "email": "jane.doe@arengu.com",
  "password": "foobar",
  "meta": {
    "company": "Arengu",
    "city": "A Coruña"
  }
}
```

#### Response sample

```json
{
  "user": {
    "id": 4,
    "name": "jane.doe@arengu.com"
  },
  "cookie": {
    "expiration": "1583927008",
    "name": "wordpress_logged_in_37d007a56d816107ce5b52c10342db37",
    "value": "jane.doe@arengu.com|1583927008|WfkNPD3irf80LxRnqC80jMmFcruPzTx1Nhhqq6n3u2p|211f9a560324e9daa43c09e4da4a7f6405d259a732b3bcaef8d3922aeee330cc"
  }
}
```

#### Error codes

| Error code | Description |
| ------ | ------ |
| existing_user_login | This email is already being used by another user |
| empty_user_login | You are not sending an email addres in the request |

### **Passwordless**

Authenticate users without password.

`POST` **/wp-json/arengu/passwordless/auth**

⚠️ **`IMPORTANT`** This endpoint is made to be used adding one authentication factor to verify the user identity (eg. one-time password via email or SMS, social login, etc).

#### Request parameters

| Parameter | Type | Description |
| ------ | ------ | ------ |
| email _(required)_| [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | The user email you want to sign up. |

#### Request sample

```json
{
  "email": "jane.doe@arengu.com"
}
```

#### Response sample

```json
{
  "user": {
    "id": 4,
    "name": "jane.doe@arengu.com"
  },
  "cookie": {
    "expiration": "1583927008",
    "name": "wordpress_logged_in_37d007a56d816107ce5b52c10342db37",
    "value": "jane.doe@arengu.com|1583927008|WfkNPD3irf80LxRnqC80jMmFcruPzTx1Nhhqq6n3u2p|211f9a560324e9daa43c09e4da4a7f6405d259a732b3bcaef8d3922aeee330cc"
  }
}
```

#### Error codes

| Error code | Description |
| ------ | ------ |
| email_not_exists | This email is not registered in your database |

### **Login**

Log in users with email and password.

`POST` **/wp-json/arengu/auth**

#### Request parameters

| Parameter | Type | Description |
| ------ | ------ | ------ |
| email _(required)_| [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | The user email you want to sign up. |
| password _(required)_ | [String](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String) | Query selector or DOM element that the form will be appended to. |

#### Request sample

```json
{
  "email": "jane.doe@arengu.com",
  "password": "foobar"
}
```

#### Response sample

```json
{
  "user": {
    "id": 4,
    "name": "jane.doe@arengu.com"
  },
  "cookie": {
    "expiration": "1583927008",
    "name": "wordpress_logged_in_37d007a56d816107ce5b52c10342db37",
    "value": "jane.doe@arengu.com|1583927008|WfkNPD3irf80LxRnqC80jMmFcruPzTx1Nhhqq6n3u2p|211f9a560324e9daa43c09e4da4a7f6405d259a732b3bcaef8d3922aeee330cc"
  }
}
```

#### Error codes

| Error code | Description |
| ------ | ------ |
| empty_username | You are not sending an email address in the request |
| empty_password | You are sending an empty or no password in the request |
| invalid_email | You are providing an invalid email address |
| incorrect_password | You are providing an invalid password |

### **Check existing email**

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
| invalid_email | You are providing an invalid or no email address |

## **Embed methods**

### **Method 1: Using a shortcode tag (Recommended)**
Place the following shortcode tag where you want your form to appear:

```
[arengu-form id="158218826036370465"]
```

You have to replace `YOUR_FORM_ID` with your **Form ID**, which you can find in your form settings or share page.

### **Method 2: Using an HTML tag**
Place the following HTML tag where you want your form to appear:

```html
<div data-arengu-form-id="YOUR_FORM_ID"></div>
```

### **Method 3:** Calling our `embed` method

Our SDK has an embed method that allows to embed your form inside any element.

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