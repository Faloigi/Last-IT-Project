<script setup lang="tsx">

</script>

<template>
  <div class="auth-container">
    <div class="auth-card">
      <h2 class="auth-title">Registrati</h2>
      <input class="auth-input" type="text" placeholder="Username" />
      <input class="auth-input" type="email" placeholder="Email" />
      <input class="auth-input" type="password" placeholder="Password" />
      <div class="spacer"></div>
      <button class="auth-btn">Registrati</button>
    </div>
    <div class="divider">o</div>
    <div class="auth-card">
      <h2 class="auth-title">Login</h2>
      <input class="auth-input" type="text" placeholder="Username o Email" />
      <input class="auth-input" type="password" placeholder="Password" />
      <div class="spacer"></div>
      <button class="auth-btn">Accedi</button>
    </div>
  </div>
</template>

<style scoped>
.auth-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #02190f;
  gap: 2rem;
}
.auth-card {
  background: #323232;
  border-radius: 70px 70px 70px 70px;
  padding: 2.5rem 3.5rem 2.5rem 3.5rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  width: 520px;
  min-width: 520px;
  max-width: 520px;
  height: 600px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.18);
}
.auth-title {
  color: #fff;
  font-size: 2.5rem;
  font-weight: bold;
  margin-bottom: 2.2rem;
  letter-spacing: 1px;
  text-align: center;
  font-family: 'Montserrat', Arial, sans-serif;
}
.auth-input {
  width: 80%;
  padding: 0.7rem 1.2rem;
  margin-bottom: 1.2rem;
  margin-left: auto;
  margin-right: auto;
  display: block;
  border: none;
  border-radius: 18px;
  background: #05442c;
  color: #fff;
  font-size: 2rem;
  font-family: 'Montserrat', Arial, sans-serif;
  outline: none;
  transition: background 0.2s;
}
.auth-input::placeholder {
  color: #b6e2c6;
  opacity: 1;
  font-size: 2rem;
  letter-spacing: 1px;
}
.auth-btn {
  background: #19d86b;
  color: #fff;
  border: none;
  border-radius: 16px;
  padding: 0.6rem 2.5rem;
  font-size: 2.2rem;
  font-family: 'Montserrat', Arial, sans-serif;
  font-weight: 500;
  cursor: pointer;
  margin-top: 2.2rem;
  margin-bottom: 0.5rem;
  margin-left: auto;
  margin-right: auto;
  display: block;
  width: 80%;
  transition: background 0.2s, color 0.2s;
  box-shadow: 0 2px 8px rgba(25,216,107,0.08);
}
.auth-btn:hover {
  background: #13b85a;
  color: #eafff3;
}
.divider {
  color: #eafff3;
  font-size: 2rem;
  font-weight: 500;
  margin: 0 1.5rem;
  align-self: center;
}
@media (max-width: 900px) {
  .auth-container {
    flex-direction: column;
    gap: 1.5rem;
  }
  .divider {
    margin: 1.5rem 0;
  }
}
</style>
