
# Phase 3: Android App Interface Setup

### Authors
Nicholas Calabro, Akash Reddy Vangala, Dhanvika Nakka

## Requirements
 
- [Android Studio](https://developer.android.com/studio) --> (latest stable)
- Android SDK --> 21+
- Java --> 11+
- Windows 11 (Recommended)
- [Phase 2 API](../phase2/README.md) --> (latest)

## Getting Started
 
### 1. Clone the repository
 
```bash
git clone https://github.com/ncalabro18/database2/
cd database2
```
 
### 2. Open in Android Studio
 
- Launch Android Studio
- Click **File -> Open** and select the cloned folder
- Wait for Gradle to sync (bottom status bar will show progress)


### 4. Set the server URL
 
In ```ApiClient.java```, modify ```BASE_URL``` to a local or remote server supporting the API.
See [Phase 2 Setup](../phase2/README.md) to run this server.
 
### 5. Run the app
 
- Connect an Android device via USB (with USB debugging enabled), or start an emulator via **Device Manager**
- Click the **Run** button or press `Shift + F10`
- Select your device and click **OK**


## Troubleshooting
 
- **Gradle sync fails** - Go to **File -> Invalidate Caches / Restart**
- **`R` cannot be resolved** - Make sure the project builds clean with **Build -> Make Project**
- **Network errors at runtime** - Confirm `INTERNET` permission is in `AndroidManifest.xml` and your server URL is correct