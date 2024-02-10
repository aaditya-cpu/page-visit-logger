#!/bin/bash

# Function to print file contents
print_file_contents() {
    local file_path="$1"
    echo "FilePath: $file_path"
    echo "Contents:"
    cat "$file_path"
    echo "" # Print a newline for better readability between files
}

# Function to traverse directory and echo structure
echo_directory_structure() {
    local directory="$1"
    for item in "$directory"/*; do
        if [ -d "$item" ]; then
            echo "Directory: $item"
            echo_directory_structure "$item" # Recurse into subdirectory
        elif [ -f "$item" ]; then
            print_file_contents "$item"
        fi
    done
}

# Check if a directory path is provided
if [ $# -eq 0 ]; then
    echo "Usage: $0 <directory_path>"
    exit 1
fi

# Start the script by echoing the structure of the provided directory
echo_directory_structure "$1"
