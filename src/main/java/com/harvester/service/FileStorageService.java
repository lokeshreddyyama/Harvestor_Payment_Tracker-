package com.harvester.service;

import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Service;

import java.io.File;
import java.io.FileOutputStream;
import java.util.Base64;
import java.util.UUID;

@Service
public class FileStorageService {

    @Value("${file.upload-dir}")
    private String uploadDir;

    public String savePhoto(String base64Image) {
        if (base64Image == null || !base64Image.startsWith("data:image")) {
            return null;
        }

        try {
            File dir = new File(uploadDir);
            if (!dir.exists()) {
                dir.mkdirs();
            }

            String[] parts = base64Image.split(",");
            String extension = parts[0].split("/")[1].split(";")[0];
            if ("jpeg".equals(extension)) {
                extension = "jpg";
            }

            String fileName = "photo_" + System.currentTimeMillis() + "_" + UUID.randomUUID().toString().substring(0, 4) + "." + extension;
            File file = new File(dir, fileName);
            
            byte[] imageBytes = Base64.getDecoder().decode(parts[1]);
            try (FileOutputStream fos = new FileOutputStream(file)) {
                fos.write(imageBytes);
            }
            
            return fileName;

        } catch (Exception e) {
            e.printStackTrace();
            return null;
        }
    }

    public void deletePhoto(String fileName) {
        if (fileName != null && !fileName.isEmpty()) {
            File file = new File(uploadDir, fileName);
            if (file.exists()) {
                file.delete();
            }
        }
    }
}
