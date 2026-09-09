import { initializeApp } from 'firebase/app';
import { getDatabase } from 'firebase/database';

const firebaseConfig = {
    apiKey: 'AIzaSyA3thj67kiWumlv2OcNJs8GYJ-XlyosYr0',
    authDomain: 'sabor-express-3e291.firebaseapp.com',
    databaseURL: 'https://sabor-express-3e291-default-rtdb.firebaseio.com',
    projectId: 'sabor-express-3e291',
    storageBucket: 'sabor-express-3e291.firebasestorage.app',
    messagingSenderId: '513379947784',
    appId: '1:513379947784:web:fe817e05a436129ce088bd',
};

const app = initializeApp(firebaseConfig);

export const database = getDatabase(app);
export default app;