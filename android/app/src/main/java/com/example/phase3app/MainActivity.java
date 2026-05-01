package com.example.phase3app;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import org.json.JSONObject;

import java.util.HashMap;
import java.util.Map;

public class MainActivity extends AppCompatActivity {

    private EditText email, password;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        email = findViewById(R.id.editEmail);
        password = findViewById(R.id.editPassword);

        Button btnLogin = findViewById(R.id.btnLogin);
        Button btnResetPassword = findViewById(R.id.btnGoResetPassword);
        Button btnRetrieveLoginInfo = findViewById(R.id.btnGoRetrieveInfo);

        btnLogin.setOnClickListener(v -> login());

        btnResetPassword.setOnClickListener(v -> {
            Intent i = new Intent(MainActivity.this, ResetPasswordActivity.class);
            startActivity(i);
        });

        btnRetrieveLoginInfo.setOnClickListener(v -> {
            Intent i = new Intent(MainActivity.this, RetrieveLoginInfoActivity.class);
            startActivity(i);
        });
    }

    private void login() {
        String emailText = email.getText().toString().trim();
        String passwordText = password.getText().toString().trim();

        if (emailText.isEmpty() || passwordText.isEmpty()) {
            Toast.makeText(this, "Enter email and password", Toast.LENGTH_LONG).show();
            return;
        }

        new Thread(() -> {
            try {
                Map<String, String> p = new HashMap<>();
                p.put("email", emailText);
                p.put("password", passwordText);

                JSONObject j = NetworkUtils.postForm(ApiClient.LOGIN, p);

                boolean success = j.getBoolean("success");
                if (!success) {
                    String msg = j.optString("message", "Login failed");
                    runOnUiThread(() -> Toast.makeText(this, msg, Toast.LENGTH_LONG).show());
                    return;
                }

                String type = j.getString("type");
                JSONObject user = j.getJSONObject("user");
                String id = user.getString("user_id");

                Intent i;
                if ("student".equals(type)) {
                    i = new Intent(MainActivity.this, StudentHomeActivity.class);
                } else if ("instructor".equals(type)) {
                    i = new Intent(MainActivity.this, InstructorHomeActivity.class);
                } else {
                    i = new Intent(MainActivity.this, AdminHomeActivity.class);
                }

                i.putExtra("user_id", id);
                i.putExtra("type", type);
                i.putExtra("email", emailText);
                startActivity(i);

            } catch (Exception e) {
                runOnUiThread(() ->
                        Toast.makeText(this, "Login error: " + e.getMessage(), Toast.LENGTH_LONG).show()
                );
            }
        }).start();
    }
}