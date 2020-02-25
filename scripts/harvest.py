#!/bin/bash

# URL to look for files
URL="https://example.com/uploads"

# File types to check
TYPES=("png" "jpg" "jpeg")

# Load possible name values from a file
NAMEVALUES=(`cat namevalues.txt`)

# Location to place downloaded files
DOWNLOADS=images

# Make the downloads folder, if it does not exist
if [ ! -d "${DOWNLOADS}" ]; then
  mkdir -p ${DOWNLOADS}
fi

# Loop through possible file names
for i in ${NAMEVALUES[@]}; do

  # Loop through possible file types
  for ext in ${TYPES[@]}; do

    # Ask the server if it has the file (HEAD)
    echo ${URL}/${i}.${ext}
    RESULT=$(curl -s -I ${URL}/${i}.${ext})

    # If the file exists on the server, download it
    #    to the downloads folder
    if [[ $RESULT == *"200 OK"* ]]; then
      wget -P ${DOWNLOADS} ${URL}/${i}.${ext}
    fi

  done

done

