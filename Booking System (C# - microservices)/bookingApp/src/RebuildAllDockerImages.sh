#!/bin/bash

# If started with argument --no-cache, the images are built without cache.

NOCACHE=''

if [ "$1" = "--no-cache" ]
then
    NOCACHE='--no-cache'

    echo "Building images without cache."
fi

docker volume create --name=sqlserverdata
docker volume create --name=rabbitmqdata

# Build the .NET SDK base image that contains the Directory.Packages.props file so it is used when restoring the NuGet packages
docker build -t dotnet-sdk-base:1.0 . -f dotnet-sdk-base-dockerfile

# Build the .NET runtime base image
docker build -t dotnet-runtime-base:1.0 . -f dotnet-runtime-base-dockerfile

# Build the .NET ASP.NET base image
docker build -t dotnet-aspnet-base:1.0 . -f dotnet-aspnet-base-dockerfile

# Rebuild all the services that have changes
docker compose build webapp $NOCACHE
