# BookingApp.Infrastructure.Messaging library
This library contains helper-classes for working with messaging within the BookingApp sample solution. It contains the following items:

- The base-classes for Commands and Events.
- Interfaces that abstract functionality to publish and consume messages using a message-broker. 
- Implementations for the interfaces that use RabbitMQ as message-broker.
- A helper class (_MessageSerializer_) for serializing and deserializing commands and events to and from JSON.

## Release notes

### Version 5.1.0
- Add ability to specify a virtual host for RabbitMQ
- Upgrade all NuGet references to the latest version.