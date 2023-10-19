export const generateRandomString = (n:number) => {
    const charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    const randomString = Array.from({ length: n }, () => {
      const randomIndex = Math.floor(Math.random() * charset.length);
      return charset.charAt(randomIndex);
    }).join('');
  
    return 'TestE2E' + randomString;
  };
  