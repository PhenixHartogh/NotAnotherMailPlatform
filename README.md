# [Not Another Mail Platform](https://namp.xyz)

**[Not Another Mail Platform](https://namp.xyz)** is a mail service that allows people to create email addresses using joke domains.  
It is powered by cPanel, hCaptcha, and Roundcube to create a public-use email platform.

---

## Table of Contents
- [1. Hosting It Yourself](#1-hosting-it-yourself)
  - [1.1. Requirements](#11-requirements)
  - [1.2. Instructions](#12-instructions)
    - [1.2.1. Creating Access Tokens](#121-creating-access-tokens)
      - [1.2.1.1. cPanel](#1211-cpanel)
      - [1.2.1.2. hCaptcha](#1212-hcaptcha)
   - [1.2.2. Filling Out the Placeholders](#122-filling-out-the-placeholders)
      - [1.2.2.1. Placeholder Meanings](#1221-placeholder-meanings)
      - [1.2.2.2. Instructions](#1222-instructions)
   - [1.2.3.3. Setting up cPanel](#1233-setting-up-cpanel)
      - [1.2.3.1. Domains](#1231-domains)
      - [1.2.3.2. DNS](#1232-dns)
  - [1.2.4. Main Domain Site](#124-main-domain-site)
      - [1.2.4.1. Website](#1241-website)
      - [1.2.4.2. Webmail](#1142-webmail)
      - [1.2.5. Testing](#125-testing)
- [2. Beta Branch](#2-beta-branch)
  - [2.1. Features](#21-features)
  - [2.2. Features in Development](#22-features-in-development)
  - [2.3. Known Issues](#23-known-issues)
  - [2.4. Beta Branch Setup Instructions](#24-beta-branch-setup-instructions)

---

## 1. Hosting It Yourself

### 1.1. Requirements
- **1.1.1.** cPanel instance with unlimited email accounts  
- **1.1.2.** cPanel account with access to all of cPanel  
- **1.1.3.** Domains that you would like to make available for registration  

### 1.2. Instructions

#### 1.2.1. Creating Access Tokens

##### 1.2.1.1. cPanel
1. Login to your cPanel instance.  
2. Scroll down to **Security** and click **Manage API Tokens**.  
3. Click **Create**.  
4. Name the token (e.g., `namp`), select **The API token will not expire**, and press **Create**.  
5. Copy the secret to your clipboard and paste it into a text document for later.  

##### 1.2.1.2. hCaptcha
1. Go to [hcaptcha.com](https://www.hcaptcha.com) and press **Sign Up**.  
2. Select the **Free Plan** (you do not need to pay).  
3. Create your account and go to [dashboard.hcaptcha.com](https://dashboard.hcaptcha.com).  
4. Press **Add Site**, enter a name, add your domain, then click **Save**.  
5. Copy your **site key** to a text document for later.  
6. Go to your account settings and press **Generate New Secret**.  
7. Copy your **secret key** to a text document for later.  

---

## 1.2.2. Filling Out the Placeholders

### 1.2.2.1. Placeholder Meanings
- `DOMAIN` → The domain(s) that users can register with  
- `YOUR_HCAPTCHA_SITE_KEY` → hCaptcha site key  
- `your_hcaptcha_secret` → hCaptcha secret  
- `your_cpanel_user` → cPanel username used to create the API token  
- `your_cpanel_token` → API token from cPanel  
- `your_cpanel_url` → URL of your cPanel instance (e.g., `cpanel.yourdomain.com`)  

### 1.2.2.2. Instructions
1. **/mail/signup.html** — Replace `'DOMAIN'` and `'YOUR_HCAPTCHA_SITE_KEY'` accordingly.  
2. **/mail/changepass.html** — Replace `'DOMAIN'` and `'YOUR_HCAPTCHA_SITE_KEY'`.  
3. **/mail/security/backend/.env.example** — Replace placeholders with actual credentials.  
4. **/mail/security/backend/submit.php** — Replace `'DOMAIN'`.  
5. **/mail/security/backend/changepassword.php** — Replace `'DOMAIN'`.  

---

## 1.2.3. Setting up cPanel

### 1.2.3.1. Domains
1. In cPanel, go to **Domains > Domains**.  
2. Click **Create a new domain**.  
3. Add your desired domain (e.g., `example.com`).  
4. Repeat for each domain users can register with.  
5. Also add `mail.example.com`.  

### 1.2.3.2. DNS

#### 1.2.3.2.1. Main Domain
- Create an **A record** for `mail` pointing to your cPanel IP.  
- Create an **A record** for `@` pointing to your cPanel IP.  

#### 1.2.3.2.2. Email Domains
1. **MX Record** — Point to `mail.maindomain.com` with priority `0`.  
2. **Email Deliverability** — Configure DKIM, SPF, and DMARC in cPanel.  

---

## 1.2.4. Main Domain Site

### 1.2.4.1. Website
- Upload your `.zip` site package to the domain’s directory in cPanel File Manager and extract it.  

### 1.2.4.2. Webmail
1. In cPanel, open **Applications**.  
2. Install **Roundcube** at `mail/roundcube`.  

---

## 1.2.5. Testing

### 1.2.5.1. Email Creation
- Go to your main domain → **Mail > Sign Up** → Create an account → Check it in cPanel.  

### 1.2.5.2. Webmail
- Go to **Mail > Webmail** and log in.  

### 1.2.5.3. Change Password
- Go to **Mail > Change Password** and update your password.  

---

## 2. Beta Branch

### 2.1. Features
- Change Password  

### 2.2. Features in Development
- Forgot Password  
- CAPTCHA on Webmail Login  
- Delete Account  
- Two Factor Authentication  

### 2.3. Known Issues
- Change Password may not work in some cases.  

### 2.4. Beta Branch Setup Instructions
*(Follows the same process as main branch — see sections [1](#1-hosting-it-yourself) and [2](#2-filling-out-the-placeholders) above)*  

---
