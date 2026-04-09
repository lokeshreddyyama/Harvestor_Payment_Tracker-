package com.harvester.service;

import lombok.extern.slf4j.Slf4j;
import org.springframework.stereotype.Service;
import org.springframework.web.multipart.MultipartFile;

import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.nio.file.StandardCopyOption;
import java.util.Base64;
import java.util.UUID;

@Service
@Slf4j
public class FileService {

    private static final String UPLOAD_DIR = "uploads/photos/";
    private static final long MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB

    public FileService() {
        try {
            Path uploadPath = Paths.get(UPLOAD_DIR);
            if (!Files.exists(uploadPath)) {
                Files.createDirectories(uploadPath);
                log.info("Created upload directory: {}", UPLOAD_DIR);
            }
        } catch (IOException e) {
            log.error("Failed to create upload directory: {}", e.getMessage());
        }
    }

    public String saveBase64Image(String base64Data) {
        if (base64Data == null || !base64Data.startsWith("data:image")) {
            return null;
        }

        try {
            // Extract base64 data
            String[] parts = base64Data.split(",");
            if (parts.length != 2) {
                return null;
            }

            String imageData = parts[1];
            byte[] decodedBytes = Base64.getDecoder().decode(imageData);

            // Check file size
            if (decodedBytes.length > MAX_FILE_SIZE) {
                throw new IOException("File size exceeds maximum allowed size");
            }

            // Generate filename
            String extension = extractExtension(parts[0]);
            String filename = "photo_" + System.currentTimeMillis() + "_" +
                            UUID.randomUUID().toString().substring(0, 8) + extension;

            // Save file
            Path filePath = Paths.get(UPLOAD_DIR, filename);
            Files.write(filePath, decodedBytes);

            log.info("Saved image file: {}", filename);
            return filename;

        } catch (IOException | IllegalArgumentException e) {
            log.error("Failed to save base64 image: {}", e.getMessage());
            return null;
        }
    }

    public String saveMultipartFile(MultipartFile file) {
        if (file == null || file.isEmpty()) {
            return null;
        }

        try {
            // Check file size
            if (file.getSize() > MAX_FILE_SIZE) {
                throw new IOException("File size exceeds maximum allowed size");
            }

            // Check if it's an image
            String contentType = file.getContentType();
            if (contentType == null || !contentType.startsWith("image/")) {
                throw new IOException("Only image files are allowed");
            }

            // Generate filename
            String extension = extractExtensionFromContentType(contentType);
            String filename = "photo_" + System.currentTimeMillis() + "_" +
                            UUID.randomUUID().toString().substring(0, 8) + extension;

            // Save file
            Path filePath = Paths.get(UPLOAD_DIR, filename);
            Files.copy(file.getInputStream(), filePath, StandardCopyOption.REPLACE_EXISTING);

            log.info("Saved multipart file: {}", filename);
            return filename;

        } catch (IOException e) {
            log.error("Failed to save multipart file: {}", e.getMessage());
            return null;
        }
    }

    public boolean deleteFile(String filename) {
        if (filename == null || filename.trim().isEmpty()) {
            return false;
        }

        try {
            Path filePath = Paths.get(UPLOAD_DIR, filename);
            if (Files.exists(filePath)) {
                Files.delete(filePath);
                log.info("Deleted file: {}", filename);
                return true;
            }
        } catch (IOException e) {
            log.error("Failed to delete file {}: {}", filename, e.getMessage());
        }
        return false;
    }

    private String extractExtension(String dataUrl) {
        // data:image/jpeg;base64,... -> .jpg
        if (dataUrl.contains("image/jpeg")) return ".jpg";
        if (dataUrl.contains("image/jpg")) return ".jpg";
        if (dataUrl.contains("image/png")) return ".png";
        if (dataUrl.contains("image/gif")) return ".gif";
        if (dataUrl.contains("image/webp")) return ".webp";
        return ".jpg"; // default
    }

    private String extractExtensionFromContentType(String contentType) {
        if ("image/jpeg".equals(contentType)) return ".jpg";
        if ("image/jpg".equals(contentType)) return ".jpg";
        if ("image/png".equals(contentType)) return ".png";
        if ("image/gif".equals(contentType)) return ".gif";
        if ("image/webp".equals(contentType)) return ".webp";
        return ".jpg"; // default
    }
}