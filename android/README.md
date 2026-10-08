# Phase 3: Android App

The Android client for the Mock University project. It calls the PHP API in `phase3/api`, which reads and writes the `DB2` database.

### Authors
Nicholas Calabro, Akash Reddy Vangala, Dhanvika Nakka

## Requirements

- [Android Studio](https://developer.android.com/studio) (latest stable)
- Android SDK: minimum SDK 24, target SDK 36
- Java 11+
- XAMPP (Apache and MariaDB) running the API and database, set up as described in the [main README](../README.md)

## Getting Started

1. Clone the repository and open the `phase3` folder in Android Studio (**File -> Open**).
2. Wait for Gradle to sync.
3. Set up the database and API using the steps in the [main README](../README.md).
4. In `ApiClient.java`, check `BASE_URL`. The default, `http://10.0.2.2/database2/phase3/api/endpoints/`, reaches XAMPP on your computer from the emulator. On a real phone, use your computer's local IP address instead.
5. Start an emulator in **Device Manager** or connect a device with USB debugging, then press Run (`Shift + F10`).

## Troubleshooting

- **Gradle sync fails:** go to **File -> Invalidate Caches / Restart**.
- **`R` cannot be resolved:** run **Build -> Make Project**.
- **Network errors at runtime:** confirm Apache and MariaDB are running, the `INTERNET` permission is in `AndroidManifest.xml`, and `BASE_URL` points to your server.
- **Login fails:** confirm you ran all three SQL scripts, in order. The default accounts are listed in the main README.
