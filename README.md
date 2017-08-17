# Client Success CRM Integration

This project aims to illustrate the functionality required for a custom Client Success CRM to integrate with SocialSignIn.

## Sample Integration Installation

```bash
docker build -t crm-image .
docker run -e SHARED_SECRET=changeme -e CS_USERNAME=whatever -e CS_PASSWORD=whatever --rm --name crm-integration crm-image
```

Code should work on a generic-ish PHP 7 Linux server if you wish to deploy it manually. Instructions should be within the Dockerfile. 

It requires a SHARED\_SECRET environment variable to be set.

## Configuration

The SHARED\_SECRET environment variable is used to verify that SocialSignIn made the CRM request, and for SocialSignIn to  verify responses.

The signing works by adding a sha256 hash\_hmac query parameter on all requests (see: http://php.net/hash\_hmac )

The CS\_USERNAME environment variable is used to authenticate with client success.

The CS\_PASSWORD environment variable is used to authenticate with client success.

You can choose to ignore these parameters if you so wish.


When integrating within the SocialSignIn Webapp (at https://app.socialsignin.net/#/settings/inbox ), chose to add a custom CRM

 * Name: choose something meaningful.
 * Search Endpoint: https://yourserver.com/search
 * Search Endpoint Secret: somethingYouChoose (SHARED\_SECRET)
 * Iframe Endpoint: https://yourserver.com/iframe
 * Iframe Endpoint Secret: somethingYouChoose (SHARED\_SECRET)


