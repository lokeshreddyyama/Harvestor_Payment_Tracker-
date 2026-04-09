package com.harvester.config;

import org.springframework.context.annotation.Configuration;
import org.springframework.web.servlet.config.annotation.ResourceHandlerRegistry;
import org.springframework.web.servlet.config.annotation.WebMvcConfigurer;

@Configuration
public class WebConfig implements WebMvcConfigurer {

    @Override
    public void configureResourceHandlers(ResourceHandlerRegistry registry) {
        // Serve uploaded files from file system and classpath
        registry.addResourceHandler("/uploads/**")
                .addResourceLocations("file:./uploads/", "classpath:/static/uploads/");
    }
}