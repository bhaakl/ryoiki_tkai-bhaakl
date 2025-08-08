FROM dotnet-sdk-base:1.0 AS build-env
WORKDIR /app

# Copy NuGet.config
COPY NuGet.config ./
# Copy local packages
COPY ./nuget-packages ./nuget-packages

# Copy necessary files and restore as distinct layer
COPY AuditlogService/*.csproj ./
RUN dotnet restore

# Copy everything else and build
COPY AuditlogService/. ./
RUN dotnet publish -c Release -o out

# Build runtime image
FROM dotnet-runtime-base:1.0
WORKDIR /app
COPY --from=build-env /app/out .

# Start
ENTRYPOINT ["dotnet", "BookingApp.AuditlogService.dll"]
