db.createCollection("archivos", {
   validator: {
      $jsonSchema: {
         bsonType: "object",
         required: ["nombre", "estado", "sha256_hash", "num_maliciosos", "url_self"],
         properties: {
            ID: {
               bsonType: "int",
               description: "ID auto incremental"
            },
            nombre: {
               bsonType: "string",
               description: "Nombre del archivo"
            },
            estado: {
               bsonType: "string",
               description: "Estado del archivo"
            },
            sha256_hash: {
               bsonType: "string",
               description: "Valor hash SHA256 del archivo"
            },
            num_maliciosos: {
               bsonType: "int",
               description: "Número de archivos maliciosos"
            },
            url_self: {
               bsonType: "string",
               description: "URL del archivo"
            }
         }
      }
   }
})
