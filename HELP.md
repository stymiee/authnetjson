# Help

Here are some tips, solutions to common problems, and guides for testing.

## Tips

### Create a Sandbox Account

Before doing any development for the Authorize.Net suite of APIs, be sure to create a 
[Sandbox Account](https://developer.authorize.net/hello_world/sandbox/) with Authorize.Net. With it you can simulate virtually
every aspect of the Authorize.Net production APIs without incurring any fees.

### Use a webhook testing site to test webhooks

Having a full understanding of what a webhook looks like makes working with webhooks easier. You can inspect an Authorize.Net
webhook using a third party service like [RequestBin](https://requestbin.fullcontact.com/).

## FAQ

Solutions to common problems when integrating the [AuthnetJSON](https://github.com/stymiee/authnetjson) library into your project.

### php://input is empty, POST is empty, webhook has no data
This may happen because a redirect occurred and steps were not taken to persist that data across the redirect. 
Look for redirects to HTTPS or to/from the `www` subdomain in your .htaccess or web.config file. 

## Support

If you require assistance using this library I can be found at Stack Overflow. Be sure when you
[ask a question](http://stackoverflow.com/questions/ask?tags=php,authorize.net) pertaining to the usage of
this class to tag your question with the **PHP** and **Authorize.Net** tags. Make sure you follow their
[guide for asking a good question](http://stackoverflow.com/help/how-to-ask) as poorly asked questions will be closed
and I will not be able to assist you.

**Do not use Stack Overflow to report bugs.** Bugs may be reported [here](https://github.com/stymiee/authnetjson/issues/new).
