# Multiuser Dungeon

### Setup

Download or clone repository to your local machine.

First make sure php is install in terminal

`sudo apt-get install php`

`cd` to `/multiuser_dungeon/server` and run the server in terminal

`php server.php`

Server will prompt you. Enter a file you'd like to use as the world data. For example,

`../dungeon/dungeon01.json`

In another terminal window `cd` to root folder `/multiuser_dungeon` and run the client in terminal

`php client.php`

Or connect to server using telnet

`telnet 127.0.0.1 8080`

### Notes

This was tested on ubuntu 18.04.1 and macOS.

I chose a JSON format to represent the world data, to try and keep it as flexible as possible. In `dungeon01.json` I added an empty room and players can't go to it simply because there is no `players` field in that room (it's not "transparent"). You can create and enter your own dungeon JSON file (for example, for testing purposes).

I chose ReactPHP to handle websocket programming as well as potential asynchronous functions (promises), and also future HTTP support. I used websockets so that clients can chat with one another in real time, as that was a requirement of the challenge.

With asynchronous functions we should theoretically not have to worry about race conditions, although for those I would heavily test it to make sure.

Current implementation does not work with NPCs.

To flesh out the program I would add a database with a user table, as well as monster and item tables, then create my CRUD operations and re-work the game logic. Having a database would alleviate some issues. For example, we are currently storing everything in memory. One thing I would change is to store the player's data in the client, then send that data to the server to perform necessary logic based on user input. It's here that we'd need asynchronous functions, in order to update player data such as current position.

I would like to write tests but due to the time limit (2-3 days), I was unfortunately unable to do so. Given more time, I would like to go ahead and write tests.
