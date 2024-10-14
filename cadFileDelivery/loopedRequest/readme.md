# Looping request advice

Looping request is a good idea if you want a definitive answer.

You can do it in PHP with a server side loop **which we don't recommend**.
Server side loop will make the user waits on the page is coming from with a waiting icon in his searchbar... Not so
convenient.

A great alternative is to load the page and make the request client side **which we recommend**.
You could display a page with simple text explaining that the answer may take time because our api is building the
model.

We provide you quick examples of both solutions in this folder. You can read it and better understand the process. 