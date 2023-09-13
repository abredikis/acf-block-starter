# ACF Block Starter Plugin

Welcome to the ACF Block Starter repository! This plugin is designed to showcase the implementation of multiple ACF (Advanced Custom Fields) blocks with a build process. With this plugin, you can explore and understand how to create dynamic blocks using ACF, and leverage the power of inner blocks to enhance your content creation experience in WordPress.

## Features

- **Multiple ACF Blocks**: The ACF Block Starter plugin comes with a single demo block – an ACF block with nested a InnerBloc. This example serves as a foundation for building your own custom blocks.

- **InnerBlock Functionality**: The InnerBlock functionality block allows admin users to nest core or other custom blocks inside the ACF block. This enables flexible and creative layouts without the need to create separate React-based edit and PHP-based front-end templates.

- **Efficient Implementation**: One of the key advantages of this implementation is that you only need to create a single PHP template that is used both for the back-end editing and front-end rendering. This eliminates the need for maintaining separate edit and front-end layouts, resulting in a more efficient development process.

- **Optimized for Performance**: To boost performance, shared assets across multiple blocks can be imported from the /scripts and /styles directories, ensuring that assets are divided into chunks to eliminate redundant asset loading.

## Getting Started

Follow these steps to get started with the ACF Block Starter plugin:

1. **Clone the Repository**: Clone this repository to your local development environment.
    ```bash
    git clone git@github.com:abredikis/acf-block-starter.git
    ```
2. **Install Dependencies**: Navigate to the plugin directory and install the required dependencies using a package manager like npm or yarn.
    ```bash
    cd acf-block-starter
    npm install
    ```
3. **Build Assets**: Build the plugin's assets using the provided build script. Use `npm run start` for development build or `npm run build` for production build.
    ```bash
    npm run start   # For development build
    # OR
    npm run build   # For production build
    ```
4. **Activate the Plugin**: Activate the ACF Block Starter plugin from the WordPress admin dashboard.
5. **Explore the Blocks**: Once activated, you can find the Counter block and the InnerBlock functionality block available in the WordPress block editor. Experiment with these blocks to understand how they are built and how dynamic and nested functionality is implemented.

## Contributing

We welcome contributions to the ACF Block Starter plugin!

## License

This project is licensed under the [MIT License](LICENSE).

