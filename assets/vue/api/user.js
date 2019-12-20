import axios from "axios";

export default {
    create(message, email, role) {

        return axios.post("/api/users", {
            name: message,
            email: email,
            role: role
        });
    },
    findAll() {
        return axios.get("/api/users");
    },
    findOne(id) {
        return axios.get("/api/user", {
            params: {
                id: id
            }
        });
    }
};
